<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Request;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportStrategy;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingField;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class HttpFoundationRequestHandler implements RequestHandlerInterface
{
    private readonly PropertyAccessorInterface $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function handle(DataTableInterface $dataTable, mixed $request = null): void
    {
        if (null === $request) {
            return;
        }

        if (!$request instanceof Request) {
            throw new \InvalidArgumentException();
        }

        $this->filter($dataTable, $request);
        $this->sort($dataTable, $request);
        $this->personalize($dataTable, $request);
        $this->paginate($dataTable, $request);
        $this->export($dataTable, $request);
    }

    private function export(DataTableInterface $dataTable, Request $request): void
    {
        $formData = $request->request->all($dataTable->getConfig()->getExportParameterName());

        if (empty($formData)) {
            return;
        }

        $dataTable->getExportForm()->submit($formData);
    }

    private function filter(DataTableInterface $dataTable, Request $request): void
    {
        $filtrationParameterName = $dataTable->getConfig()->getFiltrationParameterName();

        $filtrationData = FiltrationData::fromArray([
            'filters' => $this->extractQueryParameter($request, "[$filtrationParameterName]", []),
        ]);

        if ($filtrationData->isEmpty()) {
            return;
        }

        $dataTable->filter($filtrationData);
    }

    private function sort(DataTableInterface $dataTable, Request $request): void
    {
        $sortParameterName = $dataTable->getConfig()->getSortParameterName();

        $sortingData = new SortingData();

        $sortField = $this->extractQueryParameter($request, "[$sortParameterName][field]");
        $sortDirection = $this->extractQueryParameter($request, "[$sortParameterName][direction]", 'DESC');

        if (null !== $sortField) {
            $sortingData->addField(new SortingField($sortField, $sortDirection));
        }

        $dataTable->sort($sortingData);
    }

    private function paginate(DataTableInterface $dataTable, Request $request): void
    {
        $pageParameterName = $dataTable->getConfig()->getPageParameterName();
        $perPageParameterName = $dataTable->getConfig()->getPerPageParameterName();

        $page = $this->extractQueryParameter($request, "[$pageParameterName]", 1);
        $perPage = $this->extractQueryParameter($request, "[$perPageParameterName]", 25);

        $dataTable->paginate(new PaginationData(
            page: (int) $page,
            perPage: (int) $perPage,
        ));
    }

    private function personalize(DataTableInterface $dataTable, Request $request): void
    {
        $formData = $request->request->all($dataTable->getConfig()->getPersonalizationParameterName());

        if (empty($formData)) {
            return;
        }

        $personalizationData = new PersonalizationData(
            columns: $dataTable->getConfig()->getColumns(),
        );

        $personalizationData->fromFormData($formData);

        $dataTable->personalize($personalizationData);
    }

    private function extractQueryParameter(Request $request, string $path, mixed $default = null): mixed
    {
        return $this->propertyAccessor->getValue($request->query->all(), $path) ?? $default;
    }
}

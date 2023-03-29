<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Request;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportStrategy;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
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
            throw new UnexpectedTypeException($request, Request::class);
        }

        $this->filter($dataTable, $request);
        $this->sort($dataTable, $request);
        $this->personalize($dataTable, $request);
        $this->paginate($dataTable, $request);
        $this->export($dataTable, $request);
    }

    private function filter(DataTableInterface $dataTable, Request $request): void
    {
        $filtrationParameterName = $dataTable->getConfig()->getFiltrationParameterName();

        if (null === $data = $this->extractQueryParameter($request, "[$filtrationParameterName]")) {
            return;
        }

        $dataTable->filter($data);
    }

    private function sort(DataTableInterface $dataTable, Request $request): void
    {
        $sortParameterName = $dataTable->getConfig()->getSortParameterName();

        $sortField = $this->extractQueryParameter($request, "[$sortParameterName][field]");
        $sortDirection = $this->extractQueryParameter($request, "[$sortParameterName][direction]");

        if (null === $sortField) {
            return;
        }

        $dataTable->sort(SortingData::fromArray([
            $sortField => $sortDirection,
        ]));
    }

    private function paginate(DataTableInterface $dataTable, Request $request): void
    {
        $pageParameterName = $dataTable->getConfig()->getPageParameterName();
        $perPageParameterName = $dataTable->getConfig()->getPerPageParameterName();

        $page = $this->extractQueryParameter($request, "[$pageParameterName]");
        $perPage = $this->extractQueryParameter($request, "[$perPageParameterName]") ?? PaginationInterface::DEFAULT_PER_PAGE;

        if (null === $page) {
            return;
        }

        $dataTable->paginate(PaginationData::fromArray([
            'page' => $page,
            'perPage' => $perPage,
        ]));
    }

    private function personalize(DataTableInterface $dataTable, Request $request): void
    {
        $form = $dataTable->createPersonalizationFormBuilder()->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $dataTable->personalize($form->getData());
        }
    }

    private function export(DataTableInterface $dataTable, Request $request): void
    {
        $parameterName = $dataTable->getConfig()->getExportParameterName();

        $data = $request->request->all($parameterName);

        if (empty($data)) {
            return;
        }

        if (null !== $data['exporter'] ?? null) {
            $data['exporter'] = $dataTable->getConfig()->getExporter($data['exporter']);
        }

        $dataTable->export(ExportData::fromArray($data));
    }

    private function extractQueryParameter(Request $request, string $path, mixed $default = null): mixed
    {
        return $this->propertyAccessor->getValue($request->query->all(), $path) ?? $default;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Request;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
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

        $dataTable->getExportForm()->handleRequest($request);
    }

    private function filter(DataTableInterface $dataTable, Request $request): void
    {
        $filtrationParameterName = $dataTable->getConfig()->getFiltrationParameterName();

        $data = FiltrationData::fromArray(
            $this->extractQueryParameter($request, "[$filtrationParameterName]", [])
        );

        if ($data->isEmpty()) {
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
            'fields' => [
                [
                    'name' => $sortField,
                    'direction' => $sortDirection,
                ],
            ],
        ]));
    }

    private function paginate(DataTableInterface $dataTable, Request $request): void
    {
        $pageParameterName = $dataTable->getConfig()->getPageParameterName();
        $perPageParameterName = $dataTable->getConfig()->getPerPageParameterName();

        $defaultPaginationData = $dataTable->getConfig()->getDefaultPaginationData();

        $defaultPage = $defaultPaginationData?->getPage() ?? PaginationInterface::DEFAULT_PAGE;
        $defaultPerPage = $defaultPaginationData?->getPerPage() ?? PaginationInterface::DEFAULT_PER_PAGE;

        $page = $this->extractQueryParameter($request, "[$pageParameterName]", $defaultPage);
        $perPage = $this->extractQueryParameter($request, "[$perPageParameterName]", $defaultPerPage);

        $dataTable->paginate(PaginationData::fromArray([
            'page' => $page,
            'perPage' => $perPage,
        ]));
    }

    private function personalize(DataTableInterface $dataTable, Request $request): void
    {
        $data = array_intersect_key(
            $request->request->all($dataTable->getConfig()->getPersonalizationParameterName()),
            ['columns' => true],
        );

        if (empty($data)) {
            return;
        }

        $dataTable->personalize(PersonalizationData::fromArray($data));
    }

    private function extractQueryParameter(Request $request, string $path, mixed $default = null): mixed
    {
        return $this->propertyAccessor->getValue($request->query->all(), $path) ?? $default;
    }
}

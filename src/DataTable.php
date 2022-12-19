<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DataTable implements DataTableInterface
{
    public function __construct(
        private readonly ProxyQueryInterface $query,
        private readonly array $columns,
        private readonly array $filters,
    ) {
    }

    public function filter(array $data): void
    {
        foreach ($this->filters as $filter) {
            $filterFormName = $filter->getFormName();

            $filterData = FilterData::fromArray($data[$filterFormName] ?? []);

            if ($filterData->hasValue()) {
                $filter->apply($this->query, $filterData);
            }
        }
    }

    public function sort(string $field, string $direction): void
    {
        $this->query->sort($field, $direction);
    }

    public function paginate(int $page, int $perPage): void
    {
        $this->query->paginate($page, $perPage);
    }

    public function getPagination(): PaginationInterface
    {
        return $this->query->getPagination();
    }

    public function handleRequest(Request $request): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $parameters = $request->query->all();

        $filters = $propertyAccessor->getValue($parameters, '[filter]') ?? [];

        $this->filter($filters);

        $sortField = $propertyAccessor->getValue($parameters, '[sort][field]');
        $sortDirection = $propertyAccessor->getValue($parameters, '[sort][direction]');

        if (null !== $sortField && null !== $sortDirection) {
            $this->sort($sortField, $sortDirection);
        }

        $page = $propertyAccessor->getValue($parameters, '[page]') ?? 1;
        $perPage = $propertyAccessor->getValue($parameters, '[limit]') ?? 25;

        $this->paginate((int) $page, (int) $perPage);
    }
}
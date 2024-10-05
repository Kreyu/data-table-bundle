<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query;

use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ArrayProxyQuery implements ProxyQueryInterface
{
    private ?array $originalData = null;
    private ?array $sortedData = null;

    public function __construct(
        private array $data,
        private ?int $totalItemCount = null,
    ) {
        $this->originalData = $this->data;
        $this->sortedData = $this->data;
        $this->totalItemCount ??= count($this->data);
    }

    public function sort(SortingData $sortingData): void
    {
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        $this->originalData ??= $this->data;

        $this->data = $this->originalData;

        usort($this->data, function ($a, $b) use ($sortingData, $propertyAccessor) {
            foreach ($sortingData->getColumns() as $sortingColumnData) {
                $propertyPath = $sortingColumnData->getPropertyPath();
                $direction = $sortingColumnData->getDirection();

                $valueA = $propertyAccessor->getValue($a, $propertyPath);
                $valueB = $propertyAccessor->getValue($b, $propertyPath);

                if ($valueA < $valueB) {
                    return $direction === 'asc' ? -1 : 1;
                } elseif ($valueA > $valueB) {
                    return $direction === 'asc' ? 1 : -1;
                }
            }

            return 0;
        });

        $this->sortedData = $this->data;
    }

    public function paginate(PaginationData $paginationData): void
    {
        $this->data = array_slice(
            $this->sortedData ?? $this->originalData,
            $paginationData->getOffset(),
            $paginationData->getPerPage(),
        );
    }

    public function getResult(): ResultSetInterface
    {
        return new ResultSet(
            iterator: new \ArrayIterator($this->data),
            currentPageItemCount: count($this->data),
            totalItemCount: $this->totalItemCount,
        );
    }
}
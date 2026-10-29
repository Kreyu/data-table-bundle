<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query;

use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ArrayProxyQuery implements ProxyQueryInterface
{
    private array $originalData;
    private array $sortedData;
    private ?PaginationData $paginationData = null;

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

        $this->data = $this->originalData;

        usort($this->data, static function (array|object $itemA, array|object $itemB) use (
            $sortingData,
            $propertyAccessor,
        ): int {
            foreach ($sortingData->getColumns() as $sortingColumnData) {
                $propertyPath = $sortingColumnData->getPropertyPath();

                /** @var mixed $valueA */
                $valueA = $propertyAccessor->getValue($itemA, $propertyPath);

                /** @var mixed $valueB */
                $valueB = $propertyAccessor->getValue($itemB, $propertyPath);

                $comparison = $valueA <=> $valueB;

                if ($comparison === 0) {
                    continue;
                }

                $isAscending = $sortingColumnData->getDirection() === 'asc';

                return $isAscending ? $comparison : -$comparison;
            }

            return 0;
        });

        $this->sortedData = $this->data;

        if ($this->paginationData !== null) {
            $this->paginate($this->paginationData);
        }
    }

    public function paginate(PaginationData $paginationData): void
    {
        $this->paginationData = $paginationData;

        $this->data = array_slice(
            $this->sortedData,
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

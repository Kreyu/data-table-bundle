<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class SortingData
{
    /**
     * @var array<string, SortingColumnData>
     */
    private array $columns = [];

    /**
     * @param array<SortingColumnData> $columns
     */
    public function __construct(array $columns = [])
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * @param array<array-key, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $columns = [];

        foreach (array_keys($data) as $key) {
            $column = self::createColumnData($key, $data[$key]);

            if ($column !== null) {
                $columns[] = $column;
            }
        }

        return new self($columns);
    }

    /**
     * @param array<mixed> $columns
     */
    public function removeRedundantColumns(array $columns): void
    {
        $columnsByName = [];

        /** @var mixed $column */
        foreach ($columns as $column) {
            if (!$column instanceof ColumnInterface) {
                throw new UnexpectedTypeException($column, ColumnInterface::class);
            }

            $columnsByName[$column->getName()] = $column;
        }

        foreach (array_diff_key($this->columns, $columnsByName) as $sortingColumn) {
            $this->removeColumn($sortingColumn);
        }

        foreach ($this->columns as $sortingColumn) {
            $isSortable = $columnsByName[$sortingColumn->getName()]->getConfig()->isSortable();

            if (!$isSortable) {
                $this->removeColumn($sortingColumn);
            }
        }
    }

    /**
     * @param array<ColumnInterface> $columns
     */
    public function ensureValidPropertyPaths(array $columns): void
    {
        foreach ($this->columns as $sortingColumn) {
            $columnName = $sortingColumn->getName();

            if (!array_key_exists($columnName, $columns)) {
                throw new UnexpectedTypeException(null, ColumnInterface::class);
            }

            $sortingColumn->setPropertyPath($columns[$columnName]->getSortPropertyPath());
        }
    }

    /**
     * @return array<string, SortingColumnData>
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getColumn(string|ColumnInterface $column): ?SortingColumnData
    {
        if ($column instanceof ColumnInterface) {
            $column = $column->getName();
        }

        return $this->columns[$column] ?? null;
    }

    public function hasColumn(string|ColumnInterface $column): bool
    {
        if ($column instanceof ColumnInterface) {
            $column = $column->getName();
        }

        return array_key_exists($column, $this->columns);
    }

    public function addColumn(SortingColumnData $column): void
    {
        $this->columns[$column->getName()] = $column;
    }

    public function removeColumn(SortingColumnData $column): void
    {
        unset($this->columns[$column->getName()]);
    }

    private static function createColumnData(int|string $key, mixed $value): ?SortingColumnData
    {
        if ($value instanceof SortingColumnData) {
            return $value;
        }

        if (is_array($value)) {
            return SortingColumnData::fromArray($value);
        }

        if (is_string($key)) {
            return SortingColumnData::fromArray([
                'name' => $key,
                'direction' => $value,
                'property_path' => null,
            ]);
        }

        return null;
    }
}

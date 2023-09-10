<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class SortingData
{
    /**
     * @var array<SortingColumnData>
     */
    private array $columns = [];

    /**
     * @param array<SortingColumnData> $columns
     */
    public function __construct(array $columns = [])
    {
        foreach ($columns as $name => $column) {
            $this->addColumn($name, $column);
        }
    }

    public static function fromArray(array $data): self
    {
        $fields = [];

        foreach ($data as $key => $value) {
            if ($value instanceof SortingColumnData) {
                $fields[$value->getName()] = $value;
            } elseif (is_array($value)) {
                $fields[$key] = SortingColumnData::fromArray($value);
            } elseif (is_string($key)) {
                $fields[$key] = SortingColumnData::fromArray([
                    'name' => $key,
                    'direction' => $value,
                ]);
            }
        }

        return new self($fields);
    }

    /**
     * @param array<ColumnInterface> $columns
     */
    public function removeRedundantColumns(array $columns): void
    {
        foreach ($columns as $column) {
            if (!$column instanceof ColumnInterface) {
                throw new UnexpectedTypeException($column, ColumnInterface::class);
            }

            // Ensure that all columns are indexed by their name
            $columns[$column->getName()] = $column;
        }

        foreach (array_diff_key($this->columns, $columns) as $sortingColumn) {
            $this->removeColumn($sortingColumn);
        }

        foreach ($this->columns as $sortingColumn) {
            $column = $columns[$sortingColumn->getName()];

            if (!$column->getConfig()->isSortable()) {
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
            $column = $columns[$sortingColumn->getName()];

            if (!$column instanceof ColumnInterface) {
                throw new UnexpectedTypeException($column, ColumnInterface::class);
            }

            $sortingColumn->setPropertyPath($column->getSortPropertyPath());
        }
    }

    /**
     * @return array<SortingColumnData>
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

    public function hasColumn(ColumnInterface $column): bool
    {
        return array_key_exists($column->getName(), $this->columns);
    }

    public function addColumn(string $name, SortingColumnData $column): void
    {
        $this->columns[$name] = $column;
    }

    public function removeColumn(SortingColumnData $column): void
    {
        unset($this->columns[$column->getName()]);
    }
}

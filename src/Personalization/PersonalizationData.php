<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class PersonalizationData
{
    /**
     * @var array<PersonalizationColumnData>
     */
    private array $columns = [];

    /**
     * @param array<PersonalizationColumnData> $columns
     */
    public function __construct(array $columns = [])
    {
        foreach ($columns as $column) {
            $this->addColumn($column);
        }
    }

    /**
     * @param array<string, ColumnInterface>|array<string, array<string, mixed>> $data
     */
    public static function fromArray(array $data): self
    {
        $columns = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value['name'] ??= $key;
                $value = PersonalizationColumnData::fromArray($value);
            } elseif ($value instanceof ColumnInterface) {
                $value = PersonalizationColumnData::fromColumn($value);
            }

            $columns[$key] = $value;
        }

        return new self($columns);
    }

    /**
     * Creates a new instance from a {@see DataTableInterface}.
     * The columns are be added in order they are defined in the data table.
     * Every column is marked as "visible" by default.
     */
    public static function fromDataTable(DataTableInterface $dataTable): self
    {
        $columns = [];

        foreach (array_values($dataTable->getColumns()) as $index => $column) {
            $columns[] = PersonalizationColumnData::fromColumn($column, $index);
        }

        return new self($columns);
    }

    /**
     * Retrieves every defined column personalization data.
     *
     * @return PersonalizationColumnData[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * Retrieves a column personalization data by its name.
     */
    public function getColumn(string|ColumnInterface $column): ?PersonalizationColumnData
    {
        if ($column instanceof ColumnInterface) {
            $column = $column->getName();
        }

        return $this->columns[$column] ?? null;
    }

    /**
     * Adds a column personalization data to the stack.
     */
    public function addColumn(PersonalizationColumnData $column): void
    {
        $this->columns[$column->getName()] = $column;
    }

    /**
     * Removes a column personalization data from the stack.
     */
    public function removeColumn(PersonalizationColumnData $column): void
    {
        unset($this->columns[$column->getName()]);
    }

    /**
     * Adds columns not present in the personalization data to the stack.
     *
     * @param array<ColumnInterface> $columns
     */
    public function addMissingColumns(array $columns, bool $visible = true): void
    {
        foreach ($columns as $column) {
            if (null === $this->getColumn($column)) {
                $this->appendColumn($column, $visible);
            }
        }
    }

    /**
     * Removes columns from the personalization data that does not exist in the given set of columns.
     *
     * @param array<ColumnInterface> $columns
     */
    public function removeRedundantColumns(array $columns): void
    {
        foreach (array_diff_key($this->columns, $columns) as $column) {
            $this->removeColumn($column);
        }
    }

    /**
     * Computes given set of {@see ColumnInterface}, ordering it and excluding hidden ones.
     *
     * @param array<ColumnInterface> $columns
     *
     * @return array<ColumnInterface>
     */
    public function compute(array $columns): array
    {
        foreach ($columns as $column) {
            if (!$column instanceof ColumnInterface) {
                throw new UnexpectedTypeException($column, ColumnInterface::class);
            }
        }

        $columns = array_filter($columns, function (ColumnInterface $column) {
            return $this->isColumnVisible($column);
        });

        uasort($columns, function (ColumnInterface $columnA, ColumnInterface $columnB) {
            return $this->getColumnOrder($columnA) <=> $this->getColumnOrder($columnB);
        });

        return $columns;
    }

    public function getColumnOrder(string|ColumnInterface $column): int
    {
        return $this->getColumn($column)?->getOrder() ?? 0;
    }

    public function setColumnOrder(string|ColumnInterface $column, int $order): self
    {
        $this->getColumn($column)?->setOrder($order);

        return $this;
    }

    public function getColumnVisibility(string|ColumnInterface $column): bool
    {
        return $this->getColumn($column)?->isVisible() ?? true;
    }

    public function setColumnVisibility(string|ColumnInterface $column, bool $visible): self
    {
        $this->getColumn($column)?->setVisible($visible);

        return $this;
    }

    public function isColumnVisible(string|ColumnInterface $column): bool
    {
        return true === $this->getColumnVisibility($column);
    }

    public function isColumnHidden(string|ColumnInterface $column): bool
    {
        return false === $this->getColumnVisibility($column);
    }

    public function setColumnVisible(string|ColumnInterface $column): self
    {
        return $this->setColumnVisibility($column, true);
    }

    public function setColumnHidden(string|ColumnInterface $column): self
    {
        return $this->setColumnVisibility($column, false);
    }

    private function appendColumn(string|ColumnInterface $column, bool $visible): void
    {
        $columnsOrders = array_map(
            fn (PersonalizationColumnData $column) => $column->getOrder(),
            array_filter(
                $this->columns,
                fn (PersonalizationColumnData $columnData) => $columnData->isVisible() === $visible,
            ),
        );

        $columnOrder = 0;

        if (!empty($columnsOrders)) {
            $columnOrder = max($columnsOrders) + 1;
        }

        $this->columns[$column->getName()] = PersonalizationColumnData::fromColumn(
            column: $column,
            order: $columnOrder,
            visible: $visible,
        );
    }
}

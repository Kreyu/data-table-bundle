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

        foreach ($dataTable->getColumns() as $column) {
            $columns[] = PersonalizationColumnData::fromColumn($column);
        }

        return new self($columns);
    }

    /**
     * Applies personalization on given column(s), updating its priority and visibility.
     */
    public function apply(ColumnInterface ...$columns): void
    {
        foreach ($columns as $column) {
            $data = $this->getColumn($column);

            $column
                ->setPriority($data->getOrder())
                ->setVisible($data->isVisible());
        }
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function hasColumn(string|ColumnInterface|PersonalizationColumnData $column): bool
    {
        if (!is_string($column)) {
            $column = $column->getName();
        }

        return array_key_exists($column, $this->columns);
    }

    public function getColumn(string|ColumnInterface|PersonalizationColumnData $column): ?PersonalizationColumnData
    {
        if (!is_string($column)) {
            $column = $column->getName();
        }

        return $this->columns[$column] ?? null;
    }

    public function addColumn(ColumnInterface|PersonalizationColumnData $column): void
    {
        if ($column instanceof ColumnInterface) {
            $column = PersonalizationColumnData::fromColumn($column);
        }

        $this->columns[$column->getName()] = $column;
    }

    public function removeColumn(string|ColumnInterface|PersonalizationColumnData $column): void
    {
        if (!is_string($column)) {
            $column = $column->getName();
        }

        unset($this->columns[$column]);
    }

    /**
     * @param array<ColumnInterface|PersonalizationColumnData> $columns
     */
    public function addMissingColumns(array $columns): void
    {
        foreach ($columns as $column) {
            if (null === $this->getColumn($column)) {
                $this->addColumn($column);
            }
        }
    }

    /**
     * @param array<ColumnInterface|PersonalizationColumnData> $columns
     */
    public function removeRedundantColumns(array $columns): void
    {
        foreach (array_diff_key($this->columns, $columns) as $column) {
            $this->removeColumn($column);
        }
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

    private function appendColumn(string|ColumnInterface $column): void
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

        $this->columns[$column->getName()] = PersonalizationColumnData::fromColumn($column;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;

class PersonalizationData
{
    private array $columns;

    /**
     * @param array<ColumnInterface> $columns
     */
    public function __construct(array $columns)
    {
        $index = 0;

        foreach ($columns as $column) {
            if (!$column instanceof ColumnInterface) {
                continue;
            }

            $this->columns[$column->getName()] = new PersonalizationColumn(
                column: $column,
                order: $index++,
                visible: true,
            );
        }
    }

    public function toFormData(): array
    {
        $data = [
            'columns' => [],
        ];

        foreach ($this->getColumns() as $personalizationColumn) {
            $data['columns'][$personalizationColumn->getColumn()->getName()] = [
                'order' => $personalizationColumn->getOrder(),
                'visible' => $personalizationColumn->isVisible(),
            ];
        }

        return $data;
    }

    public function fromFormData(array $data): void
    {
        foreach ($this->getColumns() as $personalizationColumn) {
            $columnArray = $data['columns'][$personalizationColumn->getColumn()->getName()];

            $personalizationColumn->setOrder(filter_var($columnArray['order'], FILTER_VALIDATE_INT));
            $personalizationColumn->setVisible(filter_var($columnArray['visible'], FILTER_VALIDATE_BOOLEAN));
        }
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return array<ColumnInterface>
     */
    public function getComputedColumns(): array
    {
        $columns = $this->columns;

        $columns = array_filter($columns, fn (PersonalizationColumn $column) => $column->isVisible());

        usort($columns, function (PersonalizationColumn $a, PersonalizationColumn $b) {
            return $a->getOrder() <=> $b->getOrder();
        });

        return array_map(fn (PersonalizationColumn $column) => $column->getColumn(), $columns);
    }

    public function setColumnOrder(string|ColumnInterface $column, int $order): self
    {
        if ($column instanceof ColumnInterface) {
            $column = $column->getName();
        }

        if (!array_key_exists($column, $this->columns)) {
            throw new \InvalidArgumentException("Column \"$column\" does not exist in the personalization context");
        }

        $this->columns[$column]->setOrder($order);

        return $this;
    }

    public function getColumnVisibility(string|ColumnInterface $column): bool
    {
        if ($column instanceof ColumnInterface) {
            $column = $column->getName();
        }

        return $this->columns[$column]->isVisible();
    }

    public function setColumnVisibility(string|ColumnInterface $column, bool $visible): self
    {
        if ($column instanceof ColumnInterface) {
            $column = $column->getName();
        }

        if (!array_key_exists($column, $this->columns)) {
            throw new \InvalidArgumentException("Column \"$column\" does not exist in the personalization context");
        }

        $this->columns[$column]->setVisible($visible);

        return $this;
    }

    public function isColumnVisible(string|ColumnInterface $column): bool
    {
        return $this->getColumnVisibility($column) === true;
    }

    public function setColumnVisible(string|ColumnInterface $column): self
    {
        return $this->setColumnVisibility($column, true);
    }

    public function isColumnHidden(string|ColumnInterface $column): bool
    {
        return $this->getColumnVisibility($column) === false;
    }

    public function setColumnHidden(string|ColumnInterface $column): self
    {
        return $this->setColumnVisibility($column, false);
    }
}

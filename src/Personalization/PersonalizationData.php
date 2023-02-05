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

            $this->columns[$column->getName()] = new PersonalizationColumnData(
                name: $column->getName(),
                order: $index++,
                visible: true,
            );
        }
    }

    public function fromArray(array $data): void
    {
        foreach ($this->getColumns() as $personalizationColumn) {
            $columnData = $data['columns'][$personalizationColumn->getName()];

            $personalizationColumn->setOrder(filter_var($columnData['order'], FILTER_VALIDATE_INT));
            $personalizationColumn->setVisible(filter_var($columnData['visible'], FILTER_VALIDATE_BOOLEAN));
        }
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getPersonalizationColumnData(ColumnInterface|string $column): ?PersonalizationColumnData
    {
        if ($column instanceof ColumnInterface) {
            $column = $column->getName();
        }

        return $this->columns[$column] ?? null;
    }

    /**
     * @return array<ColumnInterface>
     */
    public function getComputedColumns(): array
    {
        $columns = $this->columns;

        $columns = array_filter($columns, fn (PersonalizationColumnData $column) => $column->isVisible());

        usort($columns, function (PersonalizationColumnData $a, PersonalizationColumnData $b) {
            return $a->getOrder() <=> $b->getOrder();
        });

        return array_map(fn (PersonalizationColumnData $column) => $column->getColumn(), $columns);
    }

    /**
     * @param  array<ColumnInterface> $columns
     * @return array<ColumnInterface>
     */
    public function compute(array $columns): array
    {
        $columns = array_filter(
            $columns,
            fn (ColumnInterface $column) => $this->isColumnVisible($column),
        );
        
        uasort($columns, fn (ColumnInterface $a, ColumnInterface $b) => $this->getColumnOrder($a) <=> $this->getColumnOrder($b));

        return $columns;
    }

    public function getColumnOrder(string|ColumnInterface $column): int
    {
        return $this->getPersonalizationColumnData($column)?->getOrder() ?? 0;
    }

    public function setColumnOrder(string|ColumnInterface $column, int $order): self
    {
        $this->getPersonalizationColumnData($column)?->setOrder($order);

        return $this;
    }

    public function getColumnVisibility(string|ColumnInterface $column): bool
    {
        return $this->getPersonalizationColumnData($column)?->isVisible() ?? true;
    }

    public function setColumnVisibility(string|ColumnInterface $column, bool $visible): self
    {
        $this->getPersonalizationColumnData($column)?->setVisible($visible);

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

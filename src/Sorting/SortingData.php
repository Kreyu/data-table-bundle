<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumnData;

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
        foreach ($columns as $column) {
            $this->addColumn($column);
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

    public function addColumn(SortingColumnData $column): void
    {
        $this->columns[$column->getName()] = $column;
    }

    public function removeColumn(PersonalizationColumnData $column): void
    {
        unset($this->columns[$column->getName()]);
    }
}

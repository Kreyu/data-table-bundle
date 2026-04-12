<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\RowGroup;

class AggregationDefinition
{
    /**
     * @param string                         $columnName The column name to aggregate
     * @param AggregationType                $type       The aggregation type
     * @param string|null                    $label      Optional label for the aggregation
     * @param string|null                    $propertyPath  Property path to read the value from (defaults to column name)
     */
    public function __construct(
        private readonly string $columnName,
        private readonly AggregationType $type,
        private readonly ?string $label = null,
        private readonly ?string $propertyPath = null,
    ) {
    }

    public function getColumnName(): string
    {
        return $this->columnName;
    }

    public function getType(): AggregationType
    {
        return $this->type;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getPropertyPath(): string
    {
        return $this->propertyPath ?? $this->columnName;
    }
}

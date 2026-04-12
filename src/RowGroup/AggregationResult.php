<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\RowGroup;

class AggregationResult
{
    public function __construct(
        private readonly AggregationDefinition $definition,
        private readonly mixed $value,
    ) {
    }

    public function getDefinition(): AggregationDefinition
    {
        return $this->definition;
    }

    public function getColumnName(): string
    {
        return $this->definition->getColumnName();
    }

    public function getType(): AggregationType
    {
        return $this->definition->getType();
    }

    public function getLabel(): ?string
    {
        return $this->definition->getLabel();
    }

    public function getValue(): mixed
    {
        return $this->value;
    }
}

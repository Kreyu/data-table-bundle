<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\RowGroup;

class RowGroupingConfiguration
{
    /**
     * @param list<string>                   $fields        Field names to group by (multi-level supported)
     * @param bool                           $enabled       Whether grouping is active
     * @param \Closure|null                  $startRenderer Custom renderer for group header rows
     * @param \Closure|null                  $endRenderer   Custom renderer for group footer rows
     * @param list<AggregationDefinition>    $aggregations  Aggregation definitions for group summaries
     */
    public function __construct(
        private array $fields = [],
        private bool $enabled = true,
        private ?\Closure $startRenderer = null,
        private ?\Closure $endRenderer = null,
        private array $aggregations = [],
    ) {
    }

    /**
     * @return list<string>
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @param list<string> $fields
     */
    public function setFields(array $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function hasStartRenderer(): bool
    {
        return true; // group headers are always rendered
    }

    public function getStartRenderer(): ?\Closure
    {
        return $this->startRenderer;
    }

    public function setStartRenderer(?callable $startRenderer): static
    {
        $this->startRenderer = null !== $startRenderer ? $startRenderer(...) : null;

        return $this;
    }

    public function hasEndRenderer(): bool
    {
        return null !== $this->endRenderer;
    }

    public function getEndRenderer(): ?\Closure
    {
        return $this->endRenderer;
    }

    public function setEndRenderer(?callable $endRenderer): static
    {
        $this->endRenderer = null !== $endRenderer ? $endRenderer(...) : null;

        return $this;
    }

    /**
     * @return list<AggregationDefinition>
     */
    public function getAggregations(): array
    {
        return $this->aggregations;
    }

    /**
     * @param list<AggregationDefinition> $aggregations
     */
    public function setAggregations(array $aggregations): static
    {
        $this->aggregations = $aggregations;

        return $this;
    }

    public function addAggregation(AggregationDefinition $aggregation): static
    {
        $this->aggregations[] = $aggregation;

        return $this;
    }
}

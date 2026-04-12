<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\RowGroup;

use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class AggregationCalculator
{
    public function __construct(
        private readonly PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    /**
     * Calculates all aggregation results for a given set of rows and aggregation definitions.
     *
     * @param list<mixed>                $rows
     * @param list<AggregationDefinition> $definitions
     *
     * @return list<AggregationResult>
     */
    public function calculate(array $rows, array $definitions): array
    {
        $results = [];

        foreach ($definitions as $definition) {
            $values = $this->extractValues($rows, $definition->getPropertyPath());
            $computed = $this->compute($definition->getType(), $values);

            $results[] = new AggregationResult($definition, $computed);
        }

        return $results;
    }

    /**
     * @param list<mixed> $rows
     *
     * @return list<mixed>
     */
    private function extractValues(array $rows, string $propertyPath): array
    {
        $values = [];

        foreach ($rows as $row) {
            if (is_array($row) || is_object($row)) {
                $values[] = $this->propertyAccessor->getValue($row, $propertyPath);
            }
        }

        return $values;
    }

    /**
     * @param list<mixed> $values
     */
    private function compute(AggregationType $type, array $values): int|float|null
    {
        if ([] === $values) {
            return null;
        }

        $numericValues = array_map(static fn (mixed $v): float => (float) $v, $values);

        return match ($type) {
            AggregationType::Count => count($values),
            AggregationType::Sum => array_sum($numericValues),
            AggregationType::Average => array_sum($numericValues) / count($numericValues),
            AggregationType::Min => min($numericValues),
            AggregationType::Max => max($numericValues),
        };
    }
}

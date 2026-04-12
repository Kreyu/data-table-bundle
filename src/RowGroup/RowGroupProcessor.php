<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\RowGroup;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class RowGroupProcessor
{
    private readonly PropertyAccessorInterface $propertyAccessor;
    private readonly AggregationCalculator $aggregationCalculator;

    public function __construct(
        ?PropertyAccessorInterface $propertyAccessor = null,
    ) {
        $this->propertyAccessor = $propertyAccessor ?? PropertyAccess::createPropertyAccessor();
        $this->aggregationCalculator = new AggregationCalculator($this->propertyAccessor);
    }

    /**
     * Wraps value row iteration to insert group header and footer rows.
     *
     * @param iterable<ValueRowView>         $valueRows
     * @param RowGroupingConfiguration       $config
     * @param DataTableView                  $view
     * @param array<string, ColumnInterface>  $columns
     *
     * @return iterable<ValueRowView|RowGroupHeaderView|RowGroupFooterView>
     */
    public function process(
        iterable $valueRows,
        RowGroupingConfiguration $config,
        DataTableView $view,
        array $columns,
    ): iterable {
        $fields = $config->getFields();

        if ([] === $fields) {
            yield from $valueRows;

            return;
        }

        $columnCount = count($columns);

        // For single field grouping, use a streaming approach
        if (1 === count($fields)) {
            yield from $this->processSingleLevel($valueRows, $fields[0], $config, $view, $columnCount);

            return;
        }

        // For multi-level grouping, we need to buffer rows
        yield from $this->processMultiLevel($valueRows, $fields, $config, $view, $columnCount);
    }

    /**
     * @return iterable<ValueRowView|RowGroupHeaderView|RowGroupFooterView>
     */
    private function processSingleLevel(
        iterable $valueRows,
        string $field,
        RowGroupingConfiguration $config,
        DataTableView $view,
        int $columnCount,
    ): iterable {
        $currentGroupValue = new \stdClass(); // sentinel - no group started
        $groupRows = [];
        $groupStarted = false;

        foreach ($valueRows as $valueRow) {
            $rowData = $valueRow->data;
            $groupValue = $this->resolveFieldValue($rowData, $field);

            if (!$groupStarted || $groupValue !== $currentGroupValue) {
                // Close previous group
                if ($groupStarted && $config->hasEndRenderer()) {
                    yield $this->createFooterView($view, $currentGroupValue, $field, 0, $columnCount, $config, $groupRows);
                }

                // Emit header for new group
                $currentGroupValue = $groupValue;
                $groupRows = [];
                $groupStarted = true;

                if ($config->hasStartRenderer()) {
                    yield $this->createHeaderView($view, $currentGroupValue, $field, 0, $columnCount, $config, []);
                }
            }

            $groupRows[] = $rowData;
            yield $valueRow;
        }

        // Close the last group
        if ($groupStarted && $config->hasEndRenderer()) {
            yield $this->createFooterView($view, $currentGroupValue, $field, 0, $columnCount, $config, $groupRows);
        }
    }

    /**
     * @param list<string> $fields
     *
     * @return iterable<ValueRowView|RowGroupHeaderView|RowGroupFooterView>
     */
    private function processMultiLevel(
        iterable $valueRows,
        array $fields,
        RowGroupingConfiguration $config,
        DataTableView $view,
        int $columnCount,
    ): iterable {
        // For multi-level grouping we need to track current group values at each level
        $currentValues = array_fill(0, count($fields), new \stdClass()); // sentinels
        $groupRowsByLevel = array_fill(0, count($fields), []);
        $started = array_fill(0, count($fields), false);

        foreach ($valueRows as $valueRow) {
            $rowData = $valueRow->data;

            for ($level = 0; $level < count($fields); ++$level) {
                $groupValue = $this->resolveFieldValue($rowData, $fields[$level]);

                if (!$started[$level] || $groupValue !== $currentValues[$level]) {
                    // Close all deeper levels first (reverse order)
                    for ($closeLevel = count($fields) - 1; $closeLevel > $level; --$closeLevel) {
                        if ($started[$closeLevel] && $config->hasEndRenderer()) {
                            yield $this->createFooterView(
                                $view, $currentValues[$closeLevel], $fields[$closeLevel],
                                $closeLevel, $columnCount, $config, $groupRowsByLevel[$closeLevel],
                            );
                        }
                        $groupRowsByLevel[$closeLevel] = [];
                        $started[$closeLevel] = false;
                        $currentValues[$closeLevel] = new \stdClass();
                    }

                    // Close current level
                    if ($started[$level] && $config->hasEndRenderer()) {
                        yield $this->createFooterView(
                            $view, $currentValues[$level], $fields[$level],
                            $level, $columnCount, $config, $groupRowsByLevel[$level],
                        );
                    }

                    // Open new group at this level
                    $currentValues[$level] = $groupValue;
                    $groupRowsByLevel[$level] = [];
                    $started[$level] = true;

                    if ($config->hasStartRenderer()) {
                        yield $this->createHeaderView(
                            $view, $groupValue, $fields[$level],
                            $level, $columnCount, $config, [],
                        );
                    }

                    // Open all deeper levels
                    for ($openLevel = $level + 1; $openLevel < count($fields); ++$openLevel) {
                        $deepValue = $this->resolveFieldValue($rowData, $fields[$openLevel]);
                        $currentValues[$openLevel] = $deepValue;
                        $groupRowsByLevel[$openLevel] = [];
                        $started[$openLevel] = true;

                        if ($config->hasStartRenderer()) {
                            yield $this->createHeaderView(
                                $view, $deepValue, $fields[$openLevel],
                                $openLevel, $columnCount, $config, [],
                            );
                        }
                    }

                    break; // deeper levels already handled
                }
            }

            // Add row data to all level accumulators
            for ($level = 0; $level < count($fields); ++$level) {
                $groupRowsByLevel[$level][] = $rowData;
            }

            yield $valueRow;
        }

        // Close all remaining groups (deepest first)
        for ($level = count($fields) - 1; $level >= 0; --$level) {
            if ($started[$level] && $config->hasEndRenderer()) {
                yield $this->createFooterView(
                    $view, $currentValues[$level], $fields[$level],
                    $level, $columnCount, $config, $groupRowsByLevel[$level],
                );
            }
        }
    }

    private function createHeaderView(
        DataTableView $view,
        mixed $groupValue,
        string $fieldName,
        int $level,
        int $columnCount,
        RowGroupingConfiguration $config,
        array $groupRows,
    ): RowGroupHeaderView {
        $aggregations = $this->computeAggregations($config, $groupRows);

        $headerView = new RowGroupHeaderView($view, $groupValue, $fieldName, $level, $columnCount, $aggregations);

        if (null !== $startRenderer = $config->getStartRenderer()) {
            $headerView->vars = array_merge($headerView->vars, $startRenderer($groupValue, $fieldName, $level, $aggregations));
        }

        return $headerView;
    }

    private function createFooterView(
        DataTableView $view,
        mixed $groupValue,
        string $fieldName,
        int $level,
        int $columnCount,
        RowGroupingConfiguration $config,
        array $groupRows,
    ): RowGroupFooterView {
        $aggregations = $this->computeAggregations($config, $groupRows);

        $footerView = new RowGroupFooterView($view, $groupValue, $fieldName, $level, $columnCount, $aggregations, count($groupRows));

        if (null !== $endRenderer = $config->getEndRenderer()) {
            $footerView->vars = array_merge($footerView->vars, $endRenderer($groupValue, $fieldName, $level, $aggregations, count($groupRows)));
        }

        return $footerView;
    }

    /**
     * @param list<mixed> $groupRows
     *
     * @return list<AggregationResult>
     */
    private function computeAggregations(RowGroupingConfiguration $config, array $groupRows): array
    {
        $definitions = $config->getAggregations();

        if ([] === $definitions || [] === $groupRows) {
            return [];
        }

        return $this->aggregationCalculator->calculate($groupRows, $definitions);
    }

    private function resolveFieldValue(mixed $data, string $field): mixed
    {
        if (is_array($data) || is_object($data)) {
            return $this->propertyAccessor->getValue($data, $field);
        }

        return null;
    }
}

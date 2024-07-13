<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

interface DataTableBuilderInterface extends DataTableConfigBuilderInterface
{
    public const BATCH_CHECKBOX_COLUMN_NAME = '__batch';

    public const BATCH_CHECKBOX_COLUMN_PRIORITY = 1000;

    public const ACTIONS_COLUMN_NAME = '__actions';

    public const ACTIONS_COLUMN_PRIORITY = -1;

    public const SEARCH_FILTER_NAME = '__search';

    /**
     * @return array<ColumnBuilderInterface>
     */
    public function getColumns(): array;

    /**
     * @throws InvalidArgumentException if column of given name does not exist
     */
    public function getColumn(string $name): ColumnBuilderInterface;

    public function hasColumn(string $name): bool;

    /**
     * @param class-string<ColumnTypeInterface>|null $type
     */
    public function createColumn(string $name, ?string $type = null, array $options = []): ColumnBuilderInterface;

    /**
     * @param class-string<ColumnTypeInterface>|null $type
     */
    public function addColumn(ColumnBuilderInterface|string $column, ?string $type = null, array $options = []): static;

    public function removeColumn(string $name): static;

    /**
     * @return array<FilterBuilderInterface>
     */
    public function getFilters(): array;

    /**
     * @throws InvalidArgumentException if filter of given name does not exist
     */
    public function getFilter(string $name): FilterBuilderInterface;

    public function hasFilter(string $name): bool;

    /**
     * @param class-string<FilterTypeInterface>|null $type
     */
    public function createFilter(string $name, ?string $type = null, array $options = []): FilterBuilderInterface;

    /**
     * @param class-string<FilterTypeInterface>|null $type
     */
    public function addFilter(FilterBuilderInterface|string $filter, ?string $type = null, array $options = []): static;

    public function removeFilter(string $name): static;

    public function getSearchHandler(): ?callable;

    public function setSearchHandler(?callable $searchHandler): static;

    public function isAutoAddingSearchFilter(): bool;

    public function setAutoAddingSearchFilter(bool $autoAddingSearchFilter): static;

    /**
     * @return array<ActionBuilderInterface>
     */
    public function getActions(): array;

    /**
     * @throws InvalidArgumentException if action of given name does not exist
     */
    public function getAction(string $name): ActionBuilderInterface;

    public function hasAction(string $name): bool;

    /**
     * @param class-string<ActionTypeInterface>|null $type
     */
    public function createAction(string $name, ?string $type = null, array $options = []): ActionBuilderInterface;

    /**
     * @param class-string<ActionTypeInterface>|null $type
     */
    public function addAction(ActionBuilderInterface|string $action, ?string $type = null, array $options = []): static;

    public function removeAction(string $name): static;

    /**
     * @return array<ActionBuilderInterface>
     */
    public function getBatchActions(): array;

    /**
     * @throws InvalidArgumentException if batch action of given name does not exist
     */
    public function getBatchAction(string $name): ActionBuilderInterface;

    public function hasBatchAction(string $name): bool;

    /**
     * @param class-string<ActionTypeInterface>|null $type
     */
    public function createBatchAction(string $name, ?string $type = null, array $options = []): ActionBuilderInterface;

    /**
     * @param class-string<ActionTypeInterface>|null $type
     */
    public function addBatchAction(ActionBuilderInterface|string $action, ?string $type = null, array $options = []): static;

    public function removeBatchAction(string $name): static;

    public function isAutoAddingBatchCheckboxColumn(): bool;

    public function setAutoAddingBatchCheckboxColumn(bool $autoAddingBatchCheckboxColumn): static;

    /**
     * @return array<ActionBuilderInterface>
     */
    public function getRowActions(): array;

    /**
     * @throws InvalidArgumentException if row action of given name does not exist
     */
    public function getRowAction(string $name): ActionBuilderInterface;

    public function hasRowAction(string $name): bool;

    /**
     * @param class-string<ActionTypeInterface>|null $type
     */
    public function createRowAction(string $name, ?string $type = null, array $options = []): ActionBuilderInterface;

    /**
     * @param class-string<ActionTypeInterface>|null $type
     */
    public function addRowAction(ActionBuilderInterface|string $action, ?string $type = null, array $options = []): static;

    public function removeRowAction(string $name): static;

    public function isAutoAddingActionsColumn(): bool;

    public function setAutoAddingActionsColumn(bool $autoAddingActionsColumn): static;

    /**
     * @return array<ExporterBuilderInterface>
     */
    public function getExporters(): array;

    /**
     * @throws InvalidArgumentException if exporter of given name does not exist
     */
    public function getExporter(string $name): ExporterBuilderInterface;

    public function hasExporter(string $name): bool;

    /**
     * @param class-string<ExporterTypeInterface>|null $type
     */
    public function createExporter(string $name, ?string $type = null, array $options = []): ExporterBuilderInterface;

    /**
     * @param class-string<ExporterTypeInterface>|null $type
     */
    public function addExporter(ExporterBuilderInterface|string $exporter, ?string $type = null, array $options = []): static;

    public function removeExporter(string $name): static;

    public function getQuery(): ?ProxyQueryInterface;

    public function setQuery(?ProxyQueryInterface $query): static;

    public function getDataTable(): DataTableInterface;
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

class DataTableBuilder extends DataTableConfigBuilder implements DataTableBuilderInterface
{
    /**
     * The column builders defined for the data table.
     *
     * @var array<string, ColumnBuilderInterface|null>
     */
    private array $columns = [];

    /**
     * The data of columns that haven't been converted to column builders yet.
     *
     * @var array<string, array{0: class-string<ColumnTypeInterface>, 1: array}>
     */
    private array $unresolvedColumns = [];

    /**
     * The column builders defined for the data table.
     *
     * @var array<string, FilterBuilderInterface|null>
     */
    private array $filters = [];

    /**
     * The data of filters that haven't been converted to filter builders yet.
     *
     * @var array<string, array{0: class-string<FilterTypeInterface>, 1: array}>
     */
    private array $unresolvedFilters = [];

    /**
     * The search handler used to filter the data table using a single search query.
     */
    private ?\Closure $searchHandler = null;

    /**
     * Determines whether the builder should automatically add {@see SearchFilterType}
     * when a search handler is defined in {@see DataTableBuilder::$searchHandler}.
     */
    private bool $autoAddingSearchFilter = true;

    /**
     * The action builders defined for the data table.
     *
     * @var array<string, ActionBuilderInterface|null>
     */
    private array $actions = [];

    /**
     * The data of actions that haven't been converted to action builders yet.
     *
     * @var array<string, array{0: class-string<ActionTypeInterface>, 1: array}>
     */
    private array $unresolvedActions = [];

    /**
     * The batch action builders defined for the data table.
     *
     * @var array<string, ActionBuilderInterface|null>
     */
    private array $batchActions = [];

    /**
     * The data of batch actions that haven't been converted to action builders yet.
     *
     * @var array<string, array{0: class-string<ActionTypeInterface>, 1: array}>
     */
    private array $unresolvedBatchActions = [];

    /**
     * Determines whether the builder should automatically add {@see CheckboxColumnType}
     * when at least one batch action is defined in {@see DataTableBuilder::$batchActions}.
     */
    private bool $autoAddingBatchCheckboxColumn = true;

    /**
     * The row action builders defined for the data table.
     *
     * @var array<string, ActionBuilderInterface|null>
     */
    private array $rowActions = [];

    /**
     * The data of row actions that haven't been converted to action builders yet.
     *
     * @var array<string, array{0: class-string<ActionTypeInterface>, 1: array}>
     */
    private array $unresolvedRowActions = [];

    /**
     * Determines whether the builder should automatically add {@see ActionsColumnType}
     * when at least one row action is defined in {@see DataTableBuilder::$rowActions}.
     */
    private bool $autoAddingActionsColumn = true;

    /**
     * The exporter builders defined for the data table.
     *
     * @var array<string, ExporterBuilderInterface|null>
     */
    private array $exporters = [];

    /**
     * The data of exporters that haven't been converted to exporter builders yet.
     *
     * @var array<string, array{0: class-string<ExporterTypeInterface>, 1: array}>
     */
    private array $unresolvedExporters = [];

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        string $name,
        ResolvedDataTableTypeInterface $type,
        private ?ProxyQueryInterface $query = null,
        EventDispatcherInterface $dispatcher = new EventDispatcher(),
        array $options = [],
    ) {
        parent::__construct($name, $type, $dispatcher, $options);
    }

    public function __clone(): void
    {
        if ($this->query === null) {
            return;
        }

        $this->query = clone $this->query;
    }

    public function getQuery(): ?ProxyQueryInterface
    {
        return $this->query;
    }

    public function setQuery(?ProxyQueryInterface $query): static
    {
        $this->ensureNotLocked();

        $this->query = $query;

        return $this;
    }

    public function getColumns(): array
    {
        $this->ensureNotLocked();

        return $this->resolveColumns();
    }

    public function getColumn(string $name): ColumnBuilderInterface
    {
        $this->ensureNotLocked();

        if (!array_key_exists($name, $this->columns)) {
            throw new InvalidArgumentException(sprintf('The column with the name "%s" does not exist.', $name));
        }

        return $this->columns[$name] ?? $this->resolveColumn($name);
    }

    public function createColumn(string $name, ?string $type = null, array $options = []): ColumnBuilderInterface
    {
        $this->ensureNotLocked();

        return $this->getColumnFactory()->createNamedBuilder($name, $type ?? TextColumnType::class, $options);
    }

    public function addColumn(ColumnBuilderInterface|string $column, ?string $type = null, array $options = []): static
    {
        $this->ensureNotLocked();

        if ($column instanceof ColumnBuilderInterface) {
            $this->columns[$column->getName()] = $column;

            unset($this->unresolvedColumns[$column->getName()]);

            return $this;
        }

        $this->columns[$column] = null;
        $this->unresolvedColumns[$column] = [$type ?? TextColumnType::class, $options];

        return $this;
    }

    public function hasColumn(string $name): bool
    {
        $this->ensureNotLocked();

        return array_key_exists($name, $this->columns);
    }

    public function removeColumn(string $name): static
    {
        $this->ensureNotLocked();

        unset($this->unresolvedColumns[$name], $this->columns[$name]);

        return $this;
    }

    public function getFilters(): array
    {
        $this->ensureNotLocked();

        return $this->resolveFilters();
    }

    public function getFilter(string $name): FilterBuilderInterface
    {
        $this->ensureNotLocked();

        if (!array_key_exists($name, $this->filters)) {
            throw new InvalidArgumentException(sprintf('The filter with the name "%s" does not exist.', $name));
        }

        return $this->filters[$name] ?? $this->resolveFilter($name);
    }

    public function createFilter(string $name, ?string $type = null, array $options = []): FilterBuilderInterface
    {
        $this->ensureNotLocked();

        return $this->getFilterFactory()->createNamedBuilder($name, $type ?? FilterType::class, $options);
    }

    public function addFilter(string|FilterBuilderInterface $filter, ?string $type = null, array $options = []): static
    {
        $this->ensureNotLocked();

        if ($filter instanceof FilterBuilderInterface) {
            $this->filters[$filter->getName()] = $filter;

            unset($this->unresolvedFilters[$filter->getName()]);

            return $this;
        }

        $this->filters[$filter] = null;
        $this->unresolvedFilters[$filter] = [$type ?? FilterType::class, $options];

        return $this;
    }

    public function hasFilter(string $name): bool
    {
        $this->ensureNotLocked();

        return array_key_exists($name, $this->filters);
    }

    public function removeFilter(string $name): static
    {
        $this->ensureNotLocked();

        unset($this->unresolvedFilters[$name], $this->filters[$name]);

        return $this;
    }

    public function getSearchHandler(): ?callable
    {
        $this->ensureNotLocked();

        return $this->searchHandler;
    }

    public function setSearchHandler(?callable $searchHandler): static
    {
        $this->ensureNotLocked();

        $this->searchHandler = $searchHandler === null ? null : $searchHandler(...);

        return $this;
    }

    public function isAutoAddingSearchFilter(): bool
    {
        $this->ensureNotLocked();

        return $this->autoAddingSearchFilter;
    }

    public function setAutoAddingSearchFilter(bool $autoAddingSearchFilter): static
    {
        $this->ensureNotLocked();

        $this->autoAddingSearchFilter = $autoAddingSearchFilter;

        return $this;
    }

    public function getActions(): array
    {
        $this->ensureNotLocked();

        return $this->resolveActions();
    }

    public function getAction(string $name): ActionBuilderInterface
    {
        $this->ensureNotLocked();

        if (!array_key_exists($name, $this->actions)) {
            throw new InvalidArgumentException(sprintf('The action with the name "%s" does not exist.', $name));
        }

        return $this->actions[$name] ?? $this->resolveAction($name);
    }

    public function createAction(string $name, ?string $type = null, array $options = []): ActionBuilderInterface
    {
        $this->ensureNotLocked();

        return $this->getActionFactory()->createNamedBuilder($name, $type ?? ButtonActionType::class, $options);
    }

    public function addAction(string|ActionBuilderInterface $action, ?string $type = null, array $options = []): static
    {
        $this->ensureNotLocked();

        if ($action instanceof ActionBuilderInterface) {
            $this->actions[$action->getName()] = $action;

            unset($this->unresolvedActions[$action->getName()]);

            return $this;
        }

        $this->actions[$action] = null;
        $this->unresolvedActions[$action] = [$type ?? ButtonActionType::class, $options];

        return $this;
    }

    public function hasAction(string $name): bool
    {
        $this->ensureNotLocked();

        return array_key_exists($name, $this->actions);
    }

    public function removeAction(string $name): static
    {
        $this->ensureNotLocked();

        unset($this->unresolvedActions[$name], $this->actions[$name]);

        return $this;
    }

    public function getBatchActions(): array
    {
        $this->ensureNotLocked();

        return $this->resolveBatchActions();
    }

    public function getBatchAction(string $name): ActionBuilderInterface
    {
        $this->ensureNotLocked();

        if (!array_key_exists($name, $this->batchActions)) {
            throw new InvalidArgumentException(sprintf('The batch action with the name "%s" does not exist.', $name));
        }

        return $this->batchActions[$name] ?? $this->resolveBatchAction($name);
    }

    public function hasBatchAction(string $name): bool
    {
        $this->ensureNotLocked();

        return array_key_exists($name, $this->batchActions);
    }

    public function createBatchAction(string $name, ?string $type = null, array $options = []): ActionBuilderInterface
    {
        $this->ensureNotLocked();

        return $this
            ->getActionFactory()
            ->createNamedBuilder($name, $type ?? ButtonActionType::class, $options)
            ->setContext(ActionContext::Batch)
        ;
    }

    public function addBatchAction(
        string|ActionBuilderInterface $action,
        ?string $type = null,
        array $options = [],
    ): static {
        $this->ensureNotLocked();

        if ($action instanceof ActionBuilderInterface) {
            $this->batchActions[$action->getName()] = $action;

            unset($this->unresolvedBatchActions[$action->getName()]);

            return $this;
        }

        $this->batchActions[$action] = null;
        $this->unresolvedBatchActions[$action] = [$type ?? ButtonActionType::class, $options];

        return $this;
    }

    public function removeBatchAction(string $name): static
    {
        $this->ensureNotLocked();

        unset($this->unresolvedBatchActions[$name], $this->batchActions[$name]);

        return $this;
    }

    public function isAutoAddingBatchCheckboxColumn(): bool
    {
        $this->ensureNotLocked();

        return $this->autoAddingBatchCheckboxColumn;
    }

    public function setAutoAddingBatchCheckboxColumn(bool $autoAddingBatchCheckboxColumn): static
    {
        $this->ensureNotLocked();

        $this->autoAddingBatchCheckboxColumn = $autoAddingBatchCheckboxColumn;

        return $this;
    }

    public function getRowActions(): array
    {
        $this->ensureNotLocked();

        return $this->resolveRowActions();
    }

    public function getRowAction(string $name): ActionBuilderInterface
    {
        $this->ensureNotLocked();

        if (!array_key_exists($name, $this->rowActions)) {
            throw new InvalidArgumentException(sprintf('The row action with the name "%s" does not exist.', $name));
        }

        return $this->rowActions[$name] ?? $this->resolveRowAction($name);
    }

    public function hasRowAction(string $name): bool
    {
        $this->ensureNotLocked();

        return array_key_exists($name, $this->rowActions);
    }

    public function createRowAction(string $name, ?string $type = null, array $options = []): ActionBuilderInterface
    {
        $this->ensureNotLocked();

        return $this
            ->getActionFactory()
            ->createNamedBuilder($name, $type ?? ButtonActionType::class, $options)
            ->setContext(ActionContext::Row)
        ;
    }

    public function addRowAction(
        string|ActionBuilderInterface $action,
        ?string $type = null,
        array $options = [],
    ): static {
        $this->ensureNotLocked();

        if ($action instanceof ActionBuilderInterface) {
            $this->rowActions[$action->getName()] = $action;

            unset($this->unresolvedRowActions[$action->getName()]);

            return $this;
        }

        $this->rowActions[$action] = null;
        $this->unresolvedRowActions[$action] = [$type ?? ButtonActionType::class, $options];

        return $this;
    }

    public function removeRowAction(string $name): static
    {
        $this->ensureNotLocked();

        unset($this->unresolvedRowActions[$name], $this->rowActions[$name]);

        return $this;
    }

    public function isAutoAddingActionsColumn(): bool
    {
        $this->ensureNotLocked();

        return $this->autoAddingActionsColumn;
    }

    public function setAutoAddingActionsColumn(bool $autoAddingActionsColumn): static
    {
        $this->ensureNotLocked();

        $this->autoAddingActionsColumn = $autoAddingActionsColumn;

        return $this;
    }

    public function getExporters(): array
    {
        $this->ensureNotLocked();

        return $this->resolveExporters();
    }

    public function getExporter(string $name): ExporterBuilderInterface
    {
        $this->ensureNotLocked();

        if (!array_key_exists($name, $this->exporters)) {
            throw new InvalidArgumentException(sprintf('The exporter with the name "%s" does not exist.', $name));
        }

        return $this->exporters[$name] ?? $this->resolveExporter($name);
    }

    public function createExporter(string $name, ?string $type = null, array $options = []): ExporterBuilderInterface
    {
        $this->ensureNotLocked();

        return $this->getExporterFactory()->createNamedBuilder($name, $type ?? ExporterType::class, $options);
    }

    public function addExporter(
        string|ExporterBuilderInterface $exporter,
        ?string $type = null,
        array $options = [],
    ): static {
        $this->ensureNotLocked();

        if ($exporter instanceof ExporterBuilderInterface) {
            $this->exporters[$exporter->getName()] = $exporter;

            unset($this->unresolvedExporters[$exporter->getName()]);

            return $this;
        }

        $this->exporters[$exporter] = null;
        $this->unresolvedExporters[$exporter] = [$type ?? ExporterType::class, $options];

        return $this;
    }

    public function hasExporter(string $name): bool
    {
        $this->ensureNotLocked();

        return array_key_exists($name, $this->exporters);
    }

    public function removeExporter(string $name): static
    {
        $this->ensureNotLocked();

        unset($this->unresolvedExporters[$name], $this->exporters[$name]);

        return $this;
    }

    public function getDataTable(): DataTableInterface
    {
        $this->ensureNotLocked();

        if (null === $this->query) {
            throw new BadMethodCallException(sprintf(
                'Unable to create data table without a query. Use the "%s::setQuery()" method to set a query.',
                $this::class,
            ));
        }

        $dataTable = new DataTable(clone $this->query, $this->getDataTableConfig());

        if ($this->shouldPrependBatchCheckboxColumn()) {
            $this->prependBatchCheckboxColumn();
        }

        if ($this->shouldAppendActionsColumn()) {
            $this->appendActionsColumn();
        }

        if ($this->shouldAddSearchFilter()) {
            $this->addSearchFilter();
        }

        $columns = $this->resolveColumns();

        foreach ($columns as $column) {
            $dataTable->addColumn($column->getColumn());
        }

        foreach ($this->resolveFilters() as $filter) {
            $dataTable->addFilter($filter->getFilter());
        }

        $this->addColumnFilters($dataTable, $columns);

        foreach ($this->resolveActions() as $action) {
            $dataTable->addAction($action->getAction());
        }

        foreach ($this->resolveBatchActions() as $batchAction) {
            $dataTable->addBatchAction($batchAction->getAction());
        }

        foreach ($this->resolveRowActions() as $rowAction) {
            $dataTable->addRowAction($rowAction->getAction());
        }

        foreach ($this->resolveExporters() as $exporter) {
            $dataTable->addExporter($exporter->getExporter());
        }

        return $dataTable;
    }

    /**
     * @template T of object
     *
     * @param array<string, T|null>  $builders
     * @param \Closure(string): T    $resolveBuilder
     *
     * @return array<string, T>
     */
    private function resolveBuilders(array $builders, \Closure $resolveBuilder): array
    {
        $resolvedBuilders = [];

        foreach ($builders as $name => $builder) {
            $resolvedBuilders[$name] = $builder ?? $resolveBuilder($name);
        }

        return $resolvedBuilders;
    }

    private function resolveColumn(string $name): ColumnBuilderInterface
    {
        [$type, $options] = $this->unresolvedColumns[$name];

        unset($this->unresolvedColumns[$name]);

        return $this->columns[$name] = $this->getColumnFactory()->createNamedBuilder($name, $type, $options);
    }

    /**
     * @return array<string, ColumnBuilderInterface>
     */
    private function resolveColumns(): array
    {
        return $this->resolveBuilders($this->columns, $this->resolveColumn(...));
    }

    private function resolveFilter(string $name): FilterBuilderInterface
    {
        [$type, $options] = $this->unresolvedFilters[$name];

        unset($this->unresolvedFilters[$name]);

        return $this->filters[$name] = $this->getFilterFactory()->createNamedBuilder($name, $type, $options);
    }

    /**
     * @return array<string, FilterBuilderInterface>
     */
    private function resolveFilters(): array
    {
        return $this->resolveBuilders($this->filters, $this->resolveFilter(...));
    }

    /**
     * @param array<string, ColumnBuilderInterface> $columns
     */
    private function addColumnFilters(DataTableInterface $dataTable, array $columns): void
    {
        foreach ($columns as $column) {
            /** @var class-string<FilterTypeInterface>|null $filterType */
            $filterType = $column->getOption('filter');

            if ($filterType === null) {
                continue;
            }

            $name = $column->getName();
            $isSearchFilter = is_a($filterType, SearchFilterTypeInterface::class, allow_string: true);

            if ($isSearchFilter) {
                throw new LogicException(sprintf(
                    'The column "%s" uses the search filter "%s" as its header filter. Search filters render in the search bar; add it with "addFilter()" instead.',
                    $name,
                    $filterType,
                ));
            }

            /** @var array<string, mixed> $filterOptions */
            $filterOptions = $column->getOption('filter_options');

            $queryPath = $this->getColumnFilterQueryPath($column);

            if ($queryPath !== null) {
                $filterOptions['query_path'] ??= $queryPath;
            }

            $hasSameNamedFilter = $this->hasFilter($name);

            if ($hasSameNamedFilter) {
                throw new LogicException(sprintf(
                    'The column "%1$s" has a header filter, but a filter named "%1$s" already exists. Remove either the "filter" column option or the "%1$s" filter.',
                    $name,
                ));
            }

            $dataTable->addFilter($name, $filterType, $filterOptions);
        }
    }

    private function getColumnFilterQueryPath(ColumnBuilderInterface $column): ?string
    {
        /** @var PropertyPathInterface|string|bool|null $propertyPath */
        $propertyPath = $column->getOption('property_path');
        $isStringPropertyPath = is_string($propertyPath);

        if (!$isStringPropertyPath) {
            return null;
        }

        $hasIndex = str_contains($propertyPath, '[');

        if ($hasIndex) {
            return null;
        }

        $elements = (new PropertyPath($propertyPath))->getElements();

        return implode('.', $elements);
    }

    private function resolveAction(string $name): ActionBuilderInterface
    {
        [$type, $options] = $this->unresolvedActions[$name];

        unset($this->unresolvedActions[$name]);

        $action = $this->getActionFactory()->createNamedBuilder($name, $type, $options);
        $action->setContext(ActionContext::Global);

        return $this->actions[$name] = $action;
    }

    /**
     * @return array<string, ActionBuilderInterface>
     */
    private function resolveActions(): array
    {
        return $this->resolveBuilders($this->actions, $this->resolveAction(...));
    }

    private function resolveBatchAction(string $name): ActionBuilderInterface
    {
        [$type, $options] = $this->unresolvedBatchActions[$name];

        unset($this->unresolvedBatchActions[$name]);

        $batchAction = $this->getActionFactory()->createNamedBuilder($name, $type, $options);
        $batchAction->setContext(ActionContext::Batch);

        return $this->batchActions[$name] = $batchAction;
    }

    /**
     * @return array<string, ActionBuilderInterface>
     */
    private function resolveBatchActions(): array
    {
        return $this->resolveBuilders($this->batchActions, $this->resolveBatchAction(...));
    }

    private function resolveRowAction(string $name): ActionBuilderInterface
    {
        [$type, $options] = $this->unresolvedRowActions[$name];

        unset($this->unresolvedRowActions[$name]);

        $rowAction = $this->getActionFactory()->createNamedBuilder($name, $type, $options);
        $rowAction->setContext(ActionContext::Row);

        return $this->rowActions[$name] = $rowAction;
    }

    /**
     * @return array<string, ActionBuilderInterface>
     */
    private function resolveRowActions(): array
    {
        return $this->resolveBuilders($this->rowActions, $this->resolveRowAction(...));
    }

    private function resolveExporter(string $name): ExporterBuilderInterface
    {
        [$type, $options] = $this->unresolvedExporters[$name];

        unset($this->unresolvedExporters[$name]);

        return $this->exporters[$name] = $this->getExporterFactory()->createNamedBuilder($name, $type, $options);
    }

    /**
     * @return array<string, ExporterBuilderInterface>
     */
    private function resolveExporters(): array
    {
        return $this->resolveBuilders($this->exporters, $this->resolveExporter(...));
    }

    private function shouldPrependBatchCheckboxColumn(): bool
    {
        return (
            $this->isAutoAddingBatchCheckboxColumn()
            && $this->batchActions !== []
            && !$this->hasColumn(self::BATCH_CHECKBOX_COLUMN_NAME)
        );
    }

    private function shouldAppendActionsColumn(): bool
    {
        return (
            $this->isAutoAddingActionsColumn()
            && $this->rowActions !== []
            && !$this->hasColumn(self::ACTIONS_COLUMN_NAME)
        );
    }

    private function shouldAddSearchFilter(): bool
    {
        return $this->isAutoAddingSearchFilter()
        && null !== $this->getSearchHandler()
        && !$this->hasFilter(self::SEARCH_FILTER_NAME);
    }

    private function prependBatchCheckboxColumn(): void
    {
        $this->addColumn(self::BATCH_CHECKBOX_COLUMN_NAME, CheckboxColumnType::class, [
            'priority' => self::BATCH_CHECKBOX_COLUMN_PRIORITY,
        ]);
    }

    private function appendActionsColumn(): void
    {
        $this->addColumn(self::ACTIONS_COLUMN_NAME, ActionsColumnType::class, [
            'actions' => $this->getRowActions(),
        ]);
    }

    private function addSearchFilter(): void
    {
        $this->addFilter(self::SEARCH_FILTER_NAME, SearchFilterType::class, [
            'handler' => $this->getSearchHandler(),
        ]);
    }
}

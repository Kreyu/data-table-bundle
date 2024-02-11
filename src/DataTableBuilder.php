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
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DataTableBuilder extends DataTableConfigBuilder implements DataTableBuilderInterface
{
    /**
     * The column builders defined for the data table.
     *
     * @var array<ColumnBuilderInterface>
     */
    private array $columns = [];

    /**
     * The data of columns that haven't been converted to column builders yet.
     *
     * @var array<array{0: class-string<ColumnTypeInterface>, 1: array}>
     */
    private array $unresolvedColumns = [];

    /**
     * The column builders defined for the data table.
     *
     * @var array<FilterBuilderInterface>
     */
    private array $filters = [];

    /**
     * The data of filters that haven't been converted to filter builders yet.
     *
     * @var array<array{0: class-string<FilterTypeInterface>, 1: array}>
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
     * @var array<ActionBuilderInterface>
     */
    private array $actions = [];

    /**
     * The data of actions that haven't been converted to action builders yet.
     *
     * @var array<array{0: class-string<ActionTypeInterface>, 1: array}>
     */
    private array $unresolvedActions = [];

    /**
     * The batch action builders defined for the data table.
     *
     * @var array<ActionBuilderInterface>
     */
    private array $batchActions = [];

    /**
     * The data of batch actions that haven't been converted to action builders yet.
     *
     * @var array<array{0: class-string<ActionTypeInterface>, 1: array}>
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
     * @var array<ActionBuilderInterface>
     */
    private array $rowActions = [];

    /**
     * The data of row actions that haven't been converted to action builders yet.
     *
     * @var array<array{0: class-string<ActionTypeInterface>, 1: array}>
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
     * @var array<ExporterBuilderInterface>
     */
    private array $exporters = [];

    /**
     * The data of exporters that haven't been converted to exporter builders yet.
     *
     * @var array<array{0: class-string<ExporterTypeInterface>, 1: array}>
     */
    private array $unresolvedExporters = [];

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
        $this->query = clone $this->query;
    }

    public function getQuery(): ?ProxyQueryInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return $this->query;
    }

    public function setQuery(?ProxyQueryInterface $query): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->query = $query;

        return $this;
    }

    public function getColumns(): array
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->resolveColumns();

        return $this->columns;
    }

    public function getColumn(string $name): ColumnBuilderInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if (isset($this->unresolvedColumns[$name])) {
            return $this->resolveColumn($name);
        }

        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }

        throw new InvalidArgumentException(sprintf('The column with the name "%s" does not exist.', $name));
    }

    public function addColumn(ColumnBuilderInterface|string $column, ?string $type = null, array $options = []): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

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
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return isset($this->columns[$name]) || isset($this->unresolvedColumns[$name]);
    }

    public function removeColumn(string $name): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        unset($this->unresolvedColumns[$name], $this->columns[$name]);

        return $this;
    }

    public function getFilters(): array
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->resolveFilters();

        return $this->filters;
    }

    public function getFilter(string $name): FilterBuilderInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if (isset($this->unresolvedFilters[$name])) {
            return $this->resolveFilter($name);
        }

        if (isset($this->filters[$name])) {
            return $this->filters[$name];
        }

        throw new InvalidArgumentException(sprintf('The filter with the name "%s" does not exist.', $name));
    }

    public function addFilter(string|FilterBuilderInterface $filter, ?string $type = null, array $options = []): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

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
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return isset($this->filters[$name]) || isset($this->unresolvedFilters[$name]);
    }

    public function removeFilter(string $name): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        unset($this->unresolvedFilters[$name], $this->filters[$name]);

        return $this;
    }

    public function getSearchHandler(): ?callable
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return $this->searchHandler;
    }

    public function setSearchHandler(?callable $searchHandler): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->searchHandler = $searchHandler;

        return $this;
    }

    public function isAutoAddingSearchFilter(): bool
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return $this->autoAddingSearchFilter;
    }

    public function setAutoAddingSearchFilter(bool $autoAddingSearchFilter): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->autoAddingSearchFilter = $autoAddingSearchFilter;

        return $this;
    }

    public function getActions(): array
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->resolveActions();

        return $this->actions;
    }

    public function getAction(string $name): ActionBuilderInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if (isset($this->unresolvedActions[$name])) {
            return $this->resolveAction($name);
        }

        if (isset($this->actions[$name])) {
            return $this->actions[$name];
        }

        throw new InvalidArgumentException(sprintf('The action with the name "%s" does not exist.', $name));
    }

    public function addAction(string|ActionBuilderInterface $action, ?string $type = null, array $options = []): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

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
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return isset($this->actions[$name]) || isset($this->unresolvedActions[$name]);
    }

    public function removeAction(string $name): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        unset($this->unresolvedActions[$name], $this->actions[$name]);

        return $this;
    }

    public function getBatchActions(): array
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->resolveBatchActions();

        return $this->batchActions;
    }

    public function getBatchAction(string $name): ActionBuilderInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if (isset($this->unresolvedBatchActions[$name])) {
            return $this->resolveBatchAction($name);
        }

        if (isset($this->batchActions[$name])) {
            return $this->batchActions[$name];
        }

        throw new InvalidArgumentException(sprintf('The batch action with the name "%s" does not exist.', $name));
    }

    public function hasBatchAction(string $name): bool
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return isset($this->batchActions[$name]) || isset($this->unresolvedBatchActions[$name]);
    }

    public function addBatchAction(string|ActionBuilderInterface $action, ?string $type = null, array $options = []): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

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
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        unset($this->unresolvedActions[$name], $this->batchActions[$name]);

        return $this;
    }

    public function isAutoAddingBatchCheckboxColumn(): bool
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return $this->autoAddingBatchCheckboxColumn;
    }

    public function setAutoAddingBatchCheckboxColumn(bool $autoAddingBatchCheckboxColumn): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->autoAddingBatchCheckboxColumn = $autoAddingBatchCheckboxColumn;

        return $this;
    }

    public function getRowActions(): array
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->resolveRowActions();

        return $this->rowActions;
    }

    public function getRowAction(string $name): ActionBuilderInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if (isset($this->unresolvedRowActions[$name])) {
            return $this->resolveRowAction($name);
        }

        if (isset($this->rowActions[$name])) {
            return $this->rowActions[$name];
        }

        throw new InvalidArgumentException(sprintf('The row action with the name "%s" does not exist.', $name));
    }

    public function hasRowAction(string $name): bool
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return isset($this->rowActions[$name]) || isset($this->unresolvedRowActions[$name]);
    }

    public function addRowAction(string|ActionBuilderInterface $action, ?string $type = null, array $options = []): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

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
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        unset($this->unresolvedActions[$name], $this->rowActions[$name]);

        return $this;
    }

    public function isAutoAddingActionsColumn(): bool
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return $this->autoAddingActionsColumn;
    }

    public function setAutoAddingActionsColumn(bool $autoAddingActionsColumn): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->autoAddingActionsColumn = $autoAddingActionsColumn;

        return $this;
    }

    public function getExporters(): array
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->resolveExporters();

        return $this->exporters;
    }

    public function getExporter(string $name): ExporterBuilderInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if (isset($this->unresolvedExporters[$name])) {
            return $this->resolveExporter($name);
        }

        if (isset($this->exporters[$name])) {
            return $this->exporters[$name];
        }

        throw new InvalidArgumentException(sprintf('The exporter with the name "%s" does not exist.', $name));
    }

    public function addExporter(string|ExporterBuilderInterface $exporter, ?string $type = null, array $options = []): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if ($exporter instanceof ColumnBuilderInterface) {
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
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return isset($this->exporters[$name]) || isset($this->unresolvedExporters[$name]);
    }

    public function removeExporter(string $name): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        unset($this->unresolvedExporters[$name], $this->exporters[$name]);

        return $this;
    }

    public function getDataTable(): DataTableInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        if (null === $this->query) {
            throw new BadMethodCallException(sprintf('Unable to create data table without a query. Use the "%s::setQuery()" method to set a query.', $this::class));
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

        $this->resolveColumns();

        foreach ($this->columns as $column) {
            $dataTable->addColumn($column->getColumn());
        }

        $this->resolveFilters();

        foreach ($this->filters as $filter) {
            $dataTable->addFilter($filter->getFilter());
        }

        $this->resolveActions();

        foreach ($this->actions as $action) {
            $dataTable->addAction($action->getAction());
        }

        $this->resolveBatchActions();

        foreach ($this->batchActions as $batchAction) {
            $dataTable->addBatchAction($batchAction->getAction());
        }

        $this->resolveRowActions();

        foreach ($this->rowActions as $rowAction) {
            $dataTable->addRowAction($rowAction->getAction());
        }

        $this->resolveExporters();

        foreach ($this->exporters as $exporter) {
            $dataTable->addExporter($exporter->getExporter());
        }

        // TODO: Remove initialization logic from builder.
        //       Instead, add "initialized" flag to the data table itself to allow lazy initialization.
        $dataTable->initialize();

        return $dataTable;
    }

    private function resolveColumn(string $name): ColumnBuilderInterface
    {
        [$type, $options] = $this->unresolvedColumns[$name];

        unset($this->unresolvedColumns[$name]);

        return $this->columns[$name] = $this->getColumnFactory()->createNamedBuilder($name, $type, $options);
    }

    private function resolveColumns(): void
    {
        foreach (array_keys($this->unresolvedColumns) as $column) {
            $this->resolveColumn($column);
        }
    }

    private function resolveFilter(string $name): FilterBuilderInterface
    {
        [$type, $options] = $this->unresolvedFilters[$name];

        unset($this->unresolvedFilters[$name]);

        return $this->filters[$name] = $this->getFilterFactory()->createNamedBuilder($name, $type, $options);
    }

    private function resolveFilters(): void
    {
        foreach (array_keys($this->unresolvedFilters) as $filter) {
            $this->resolveFilter($filter);
        }
    }

    private function resolveAction(string $name): ActionBuilderInterface
    {
        [$type, $options] = $this->unresolvedActions[$name];

        unset($this->unresolvedActions[$name]);

        $action = $this->getActionFactory()->createNamedBuilder($name, $type, $options);
        $action->setContext(ActionContext::Global);

        return $this->actions[$name] = $action;
    }

    private function resolveActions(): void
    {
        foreach (array_keys($this->unresolvedActions) as $action) {
            $this->resolveAction($action);
        }
    }

    private function resolveBatchAction(string $name): ActionBuilderInterface
    {
        [$type, $options] = $this->unresolvedBatchActions[$name];

        unset($this->unresolvedBatchActions[$name]);

        $batchAction = $this->getActionFactory()->createNamedBuilder($name, $type, $options);
        $batchAction->setContext(ActionContext::Batch);

        return $this->batchActions[$name] = $batchAction;
    }

    private function resolveBatchActions(): void
    {
        foreach (array_keys($this->unresolvedBatchActions) as $batchAction) {
            $this->resolveBatchAction($batchAction);
        }
    }

    private function resolveRowAction(string $name): ActionBuilderInterface
    {
        [$type, $options] = $this->unresolvedRowActions[$name];

        unset($this->unresolvedRowActions[$name]);

        $rowAction = $this->getActionFactory()->createNamedBuilder($name, $type, $options);
        $rowAction->setContext(ActionContext::Row);

        return $this->rowActions[$name] = $rowAction;
    }

    private function resolveRowActions(): void
    {
        foreach (array_keys($this->unresolvedRowActions) as $rowAction) {
            $this->resolveRowAction($rowAction);
        }
    }

    private function resolveExporter(string $name): ExporterBuilderInterface
    {
        [$type, $options] = $this->unresolvedExporters[$name];

        unset($this->unresolvedExporters[$name]);

        return $this->exporters[$name] = $this->getExporterFactory()->createNamedBuilder($name, $type, $options);
    }

    private function resolveExporters(): void
    {
        foreach (array_keys($this->unresolvedExporters) as $exporter) {
            $this->resolveExporter($exporter);
        }
    }

    private function shouldPrependBatchCheckboxColumn(): bool
    {
        return $this->isAutoAddingBatchCheckboxColumn()
            && !empty($this->batchActions)
            && !$this->hasColumn(self::BATCH_CHECKBOX_COLUMN_NAME);
    }

    private function shouldAppendActionsColumn(): bool
    {
        return $this->isAutoAddingActionsColumn()
            && !empty($this->rowActions)
            && !$this->hasColumn(self::ACTIONS_COLUMN_NAME);
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
            'priority' => self::ACTIONS_COLUMN_PRIORITY,
            'actions' => $this->getRowActions(),
        ]);
    }

    private function addSearchFilter(): void
    {
        $this->addFilter(self::SEARCH_FILTER_NAME, SearchFilterType::class, [
            'handler' => $this->getSearchHandler(),
        ]);
    }

    private function createBuilderLockedException(): BadMethodCallException
    {
        return new BadMethodCallException('DataTableBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTableExportEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTableFiltrationEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePaginationEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePersonalizationEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTableSortingEvent;
use Kreyu\Bundle\DataTableBundle\Exception\OutOfBoundsException;
use Kreyu\Bundle\DataTableBundle\Exception\RuntimeException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportStrategy;
use Kreyu\Bundle\DataTableBundle\Exporter\Form\Type\ExportDataType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\FiltrationDataType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceContext;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Form\Type\PersonalizationDataType;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\Form\FormBuilderInterface;

class DataTable implements DataTableInterface
{
    /**
     * @var array<ColumnInterface>
     */
    private array $columns = [];

    /**
     * @var array<FilterInterface>
     */
    private array $filters = [];

    /**
     * @var array<ActionInterface>
     */
    private array $actions = [];

    /**
     * @var array<ActionInterface>
     */
    private array $batchActions = [];

    /**
     * @var array<ActionInterface>
     */
    private array $rowActions = [];

    /**
     * @var array<ExporterInterface>
     */
    private array $exporters = [];

    /**
     * The sorting data currently applied to the data table.
     */
    private ?SortingData $sortingData = null;

    /**
     * The pagination data currently applied to the data table.
     */
    private ?PaginationData $paginationData = null;

    /**
     * The filtration data currently applied to the data table.
     */
    private ?FiltrationData $filtrationData = null;

    /**
     * The personalization data currently applied to the data table.
     */
    private ?PersonalizationData $personalizationData = null;

    /**
     * The export data currently applied to the data table.
     */
    private ?ExportData $exportData = null;

    /**
     * The pagination used to retrieve the current page results.
     */
    private ?PaginationInterface $pagination = null;

    /**
     * The copy of a query used to retrieve the data, without any filters applied.
     */
    private ProxyQueryInterface $originalQuery;

    private bool $initialized = false;

    public function __construct(
        private ProxyQueryInterface $query,
        private /* readonly */ DataTableConfigInterface $config,
    ) {
        $this->originalQuery = clone $this->query;
    }

    public function __clone(): void
    {
        $this->config = clone $this->config;
        $this->query = clone $this->query;
    }

    public function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        if ($paginationData = $this->getInitialPaginationData()) {
            $this->paginate($paginationData, false);
        }

        if ($sortingData = $this->getInitialSortingData()) {
            $this->sort($sortingData, false);
        }

        if ($filtrationData = $this->getInitialFiltrationData()) {
            $this->filter($filtrationData, false);
        }

        if ($personalizationData = $this->getInitialPersonalizationData()) {
            $this->personalize($personalizationData, false);
        }

        $this->initialized = true;
    }

    public function getName(): string
    {
        return $this->config->getName();
    }

    public function getQuery(): ProxyQueryInterface
    {
        return $this->query;
    }

    public function getConfig(): DataTableConfigInterface
    {
        return $this->config;
    }

    public function getColumns(): array
    {
        $columns = $this->columns;

        uasort($columns, static function (ColumnInterface $columnA, ColumnInterface $columnB) {
            return $columnB->getPriority() <=> $columnA->getPriority();
        });

        return $columns;
    }

    public function getVisibleColumns(): array
    {
        return array_filter(
            $this->getColumns(),
            static fn (ColumnInterface $column) => $column->isVisible(),
        );
    }

    public function getHiddenColumns(): array
    {
        return array_filter(
            $this->getColumns(),
            static fn (ColumnInterface $column) => !$column->isVisible(),
        );
    }

    public function getExportableColumns(): array
    {
        return array_filter(
            $this->getVisibleColumns(),
            static fn (ColumnInterface $column) => $column->getConfig()->isExportable(),
        );
    }

    public function getColumn(string $name): ColumnInterface
    {
        if (isset($this->columns[$name])) {
            return $this->columns[$name];
        }

        throw new OutOfBoundsException(sprintf('Column "%s" does not exist.', $name));
    }

    public function hasColumn(string $name): bool
    {
        return array_key_exists($name, $this->columns);
    }

    public function addColumn(ColumnInterface|string $column, string $type = ColumnType::class, array $options = []): static
    {
        if (is_string($column)) {
            $column = $this->config->getColumnFactory()
                ->createNamedBuilder($column, $type, $options)
                ->getColumn()
            ;
        }

        $this->columns[$column->getName()] = $column;

        $column->setDataTable($this);

        return $this;
    }

    public function removeColumn(string $name): static
    {
        unset($this->columns[$name]);

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getFilter(string $name): FilterInterface
    {
        if (isset($this->filters[$name])) {
            return $this->filters[$name];
        }

        throw new OutOfBoundsException(sprintf('Filter "%s" does not exist.', $name));
    }

    public function hasFilter(string $name): bool
    {
        return array_key_exists($name, $this->filters);
    }

    public function addFilter(FilterInterface|string $filter, string $type = FilterType::class, array $options = []): static
    {
        if (is_string($filter)) {
            $filter = $this->config->getFilterFactory()
                ->createNamedBuilder($filter, $type, $options)
                ->getFilter()
            ;
        }

        $this->filters[$filter->getName()] = $filter;

        $filter->setDataTable($this);

        return $this;
    }

    public function removeFilter(string $name): static
    {
        unset($this->filters[$name]);

        return $this;
    }

    public function getActions(): array
    {
        return $this->actions;
    }

    public function getAction(string $name): ActionInterface
    {
        if (isset($this->actions[$name])) {
            return $this->actions[$name];
        }

        throw new OutOfBoundsException(sprintf('Action "%s" does not exist.', $name));
    }

    public function hasAction(string $name): bool
    {
        return array_key_exists($name, $this->actions);
    }

    public function addAction(ActionInterface|string $action, string $type = ActionType::class, array $options = []): static
    {
        if (is_string($action)) {
            $builder = $this->config->getActionFactory()->createNamedBuilder($action, $type, $options);
            $builder->setContext(ActionContext::Global);

            $action = $builder->getAction();
        }

        $this->actions[$action->getName()] = $action;

        $action->setDataTable($this);

        return $this;
    }

    public function removeAction(string $name): static
    {
        unset($this->actions[$name]);

        return $this;
    }

    public function getBatchActions(): array
    {
        return $this->batchActions;
    }

    public function getBatchAction(string $name): ActionInterface
    {
        if (isset($this->batchActions[$name])) {
            return $this->batchActions[$name];
        }

        throw new OutOfBoundsException(sprintf('Batch action "%s" does not exist.', $name));
    }

    public function hasBatchAction(string $name): bool
    {
        return array_key_exists($name, $this->batchActions);
    }

    public function addBatchAction(ActionInterface|string $action, string $type = ActionType::class, array $options = []): static
    {
        if (is_string($action)) {
            $builder = $this->config->getActionFactory()->createNamedBuilder($action, $type, $options);
            $builder->setContext(ActionContext::Batch);

            $action = $builder->getAction();
        }

        $this->batchActions[$action->getName()] = $action;

        $action->setDataTable($this);

        return $this;
    }

    public function removeBatchAction(string $name): static
    {
        unset($this->batchActions[$name]);

        return $this;
    }

    public function getRowActions(): array
    {
        return $this->rowActions;
    }

    public function getRowAction(string $name): ActionInterface
    {
        if (isset($this->rowActions[$name])) {
            return $this->rowActions[$name];
        }

        throw new OutOfBoundsException(sprintf('Row action "%s" does not exist.', $name));
    }

    public function hasRowAction(string $name): bool
    {
        return array_key_exists($name, $this->rowActions);
    }

    public function addRowAction(ActionInterface|string $action, string $type = ActionType::class, array $options = []): static
    {
        if (is_string($action)) {
            $builder = $this->config->getActionFactory()->createNamedBuilder($action, $type, $options);
            $builder->setContext(ActionContext::Row);

            $action = $builder->getAction();
        }

        $this->rowActions[$action->getName()] = $action;

        $action->setDataTable($this);

        return $this;
    }

    public function removeRowAction(string $name): static
    {
        unset($this->rowActions[$name]);

        return $this;
    }

    public function getExporters(): array
    {
        return $this->exporters;
    }

    public function getExporter(string $name): ExporterInterface
    {
        if (isset($this->exporters[$name])) {
            return $this->exporters[$name];
        }

        throw new OutOfBoundsException(sprintf('Exporter "%s" does not exist.', $name));
    }

    public function hasExporter(string $name): bool
    {
        return array_key_exists($name, $this->exporters);
    }

    public function addExporter(ExporterInterface|string $exporter, string $type = ExporterType::class, array $options = []): static
    {
        if (is_string($exporter)) {
            $exporter = $this->config->getExporterFactory()
                ->createNamedBuilder($exporter, $type, $options)
                ->getExporter()
            ;
        }

        $this->exporters[$exporter->getName()] = $exporter;

        $exporter->setDataTable($this);

        return $this;
    }

    public function removeExporter(string $name): static
    {
        unset($this->exporters[$name]);

        return $this;
    }

    public function paginate(PaginationData $data, bool $persistence = true): void
    {
        if (!$this->config->isPaginationEnabled()) {
            return;
        }

        $this->dispatch(DataTableEvents::PRE_PAGINATE, $event = new DataTablePaginationEvent($this, $data));

        $data = $event->getPaginationData();

        $this->query->paginate($data);

        $this->originalQuery = $this->query;

        if ($persistence && $this->config->isPaginationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Pagination, $data);
        }

        $this->setPaginationData($data);
        $this->resetPagination();

        $this->dispatch(DataTableEvents::POST_PAGINATE, new DataTablePaginationEvent($this, $data));
    }

    public function sort(SortingData $data, bool $persistence = true): void
    {
        if (!$this->config->isSortingEnabled()) {
            return;
        }

        $this->dispatch(DataTableEvents::PRE_SORT, $event = new DataTableSortingEvent($this, $data));

        $data = $event->getSortingData();

        $columns = $this->getColumns();

        $data->removeRedundantColumns($columns);
        $data->ensureValidPropertyPaths($columns);

        $this->query->sort($data);

        $this->originalQuery = $this->query;

        if ($persistence && $this->config->isSortingPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Sorting, $data);
        }

        $this->setSortingData($data);
        $this->resetPagination();

        $this->dispatch(DataTableEvents::POST_SORT, new DataTableSortingEvent($this, $data));
    }

    public function filter(FiltrationData $data, bool $persistence = true): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        $this->query = clone $this->originalQuery;

        $this->dispatch(DataTableEvents::PRE_FILTER, $event = new DataTableFiltrationEvent($this, $data));

        $data = $event->getFiltrationData();

        $filters = $this->getFilters();

        $data->appendMissingFilters($filters);
        $data->removeRedundantFilters($filters);

        foreach ($filters as $filter) {
            $filterData = $data->getFilterData($filter->getName());

            if ($filterData && $filterData->hasValue()) {
                $filter->apply($this->query, $filterData);
            }
        }

        if ($persistence && $this->config->isFiltrationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Filtration, $data);
        }

        $this->setFiltrationData($data);
        $this->resetPagination();

        $this->dispatch(DataTableEvents::POST_FILTER, new DataTableFiltrationEvent($this, $data));
    }

    public function personalize(PersonalizationData $data, bool $persistence = true): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        $this->dispatch(DataTableEvents::PRE_PERSONALIZE, $event = new DataTablePersonalizationEvent($this, $data));

        $data = $event->getPersonalizationData();

        $columns = $this->getColumns();

        $data->addMissingColumns($columns);
        $data->removeRedundantColumns($columns);

        if ($persistence && $this->config->isPersonalizationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Personalization, $data);
        }

        $this->setPersonalizationData($data);

        $data->apply($this->getColumns());

        $this->dispatch(DataTableEvents::POST_PERSONALIZE, new DataTablePersonalizationEvent($this, $data));
    }

    public function export(ExportData $data = null): ExportFile
    {
        if (!$this->config->isExportingEnabled()) {
            throw new RuntimeException('The data table has exporting feature disabled.');
        }

        $dataTable = clone $this;

        $data ??= $this->exportData ?? $this->config->getDefaultExportData() ?? ExportData::fromDataTable($this);

        // TODO: Remove "getNonDeprecatedCase()" call once the deprecated strategies are removed.
        $data->strategy = $data->strategy->getNonDeprecatedCase();

        $this->dispatch(DataTableEvents::PRE_EXPORT, $event = new DataTableExportEvent($dataTable, $data));

        $data = $event->getExportData();

        if (ExportStrategy::IncludeAll === $data->strategy) {
            $dataTable->getQuery()->paginate(new PaginationData(perPage: null));
        }

        if (!$data->includePersonalization) {
            $dataTable->resetPersonalization();
        }

        if (null === $data->exporter) {
            $exporter = $this->exporters[array_key_first($this->exporters)];
        } else {
            $exporter = $this->getExporter($data->exporter);
        }

        return $exporter->export($dataTable->createExportView(), $data->filename);
    }

    public function getItems(): iterable
    {
        if ($this->getConfig()->isPaginationEnabled()) {
            return $this->getPagination()->getItems();
        }

        return $this->query->getItems();
    }

    public function getPagination(): PaginationInterface
    {
        if (!$this->config->isPaginationEnabled()) {
            throw new RuntimeException('The data table has pagination feature disabled.');
        }

        return $this->pagination ??= $this->query->getPagination();
    }

    public function getSortingData(): ?SortingData
    {
        return $this->sortingData;
    }

    public function setSortingData(?SortingData $sortingData): static
    {
        $this->sortingData = $sortingData;

        return $this;
    }

    public function getPaginationData(): ?PaginationData
    {
        return $this->paginationData;
    }

    public function setPaginationData(?PaginationData $paginationData): static
    {
        $this->paginationData = $paginationData;

        return $this;
    }

    public function getFiltrationData(): ?FiltrationData
    {
        return $this->filtrationData;
    }

    public function setFiltrationData(?FiltrationData $filtrationData): static
    {
        $this->filtrationData = $filtrationData;

        return $this;
    }

    public function getPersonalizationData(): ?PersonalizationData
    {
        return $this->personalizationData;
    }

    public function setPersonalizationData(?PersonalizationData $personalizationData): static
    {
        $this->personalizationData = $personalizationData;

        return $this;
    }

    public function getExportData(): ?ExportData
    {
        return $this->exportData;
    }

    public function setExportData(?ExportData $exportData): static
    {
        $this->exportData = $exportData;

        return $this;
    }

    public function createFiltrationFormBuilder(DataTableView $view = null): FormBuilderInterface
    {
        if (!$this->config->isFiltrationEnabled()) {
            throw new RuntimeException('The data table has filtration feature disabled.');
        }

        if (null === $formFactory = $this->config->getFiltrationFormFactory()) {
            throw new RuntimeException('The data table has no configured filtration form factory.');
        }

        return $formFactory->createNamedBuilder(
            name: $this->config->getFiltrationParameterName(),
            type: FiltrationDataType::class,
            options: [
                'data_table' => $this,
                'data_table_view' => $view,
            ],
        );
    }

    public function createPersonalizationFormBuilder(DataTableView $view = null): FormBuilderInterface
    {
        if (!$this->config->isPersonalizationEnabled()) {
            throw new RuntimeException('The data table has personalization feature disabled.');
        }

        if (null === $formFactory = $this->config->getPersonalizationFormFactory()) {
            throw new RuntimeException('The data table has no configured personalization form factory.');
        }

        return $formFactory->createNamedBuilder(
            name: $this->config->getPersonalizationParameterName(),
            type: PersonalizationDataType::class,
            options: [
                'data_table_view' => $view,
            ],
        );
    }

    public function createExportFormBuilder(DataTableView $view = null): FormBuilderInterface
    {
        if (!$this->config->isExportingEnabled()) {
            throw new RuntimeException('The data table has export feature disabled.');
        }

        if (null === $formFactory = $this->config->getExportFormFactory()) {
            throw new RuntimeException('The data table has no configured export form factory.');
        }

        $data = $this->config->getDefaultExportData() ?? ExportData::fromDataTable($this);

        if (null !== $data) {
            $data->filename ??= $this->getName();
        }

        return $formFactory->createNamedBuilder(
            name: $this->config->getExportParameterName(),
            type: ExportDataType::class,
            data: $data,
            options: [
                'exporters' => $this->getExporters(),
            ],
        );
    }

    public function isExporting(): bool
    {
        return null !== $this->exportData;
    }

    public function hasActiveFilters(): bool
    {
        return (bool) $this->filtrationData?->hasActiveFilters();
    }

    public function handleRequest(mixed $request): void
    {
        if (null === $requestHandler = $this->config->getRequestHandler()) {
            throw new RuntimeException('The "handleRequest" method cannot be used on data tables without configured request handler.');
        }

        $requestHandler->handle($this, $request);
    }

    public function createView(): DataTableView
    {
        $type = $this->config->getType();
        $options = $this->config->getOptions();

        $view = $type->createView($this);

        $type->buildView($view, $this, $options);

        return $view;
    }

    public function createExportView(): DataTableView
    {
        $type = $this->config->getType();
        $options = $this->config->getOptions();

        $view = $type->createExportView($this);

        $type->buildExportView($view, $this, $options);

        return $view;
    }

    private function dispatch(string $eventName, DataTableEvent $event): void
    {
        $dispatcher = $this->config->getEventDispatcher();

        if ($dispatcher->hasListeners($eventName)) {
            $dispatcher->dispatch($event, $eventName);
        }
    }

    private function resetPagination(): void
    {
        $this->pagination = null;
    }

    private function getInitialPaginationData(): ?PaginationData
    {
        if (!$this->config->isPaginationEnabled()) {
            return null;
        }

        $data = null;

        if ($this->config->isPaginationPersistenceEnabled()) {
            $data = $this->getPersistenceData(PersistenceContext::Pagination);
        }

        $data ??= $this->config->getDefaultPaginationData();

        $data ??= new PaginationData();

        return $data;
    }

    private function getInitialSortingData(): ?SortingData
    {
        if (!$this->config->isSortingEnabled()) {
            return null;
        }

        $data = null;

        if ($this->config->isSortingPersistenceEnabled()) {
            $data = $this->getPersistenceData(PersistenceContext::Sorting);
        }

        $data ??= $this->config->getDefaultSortingData();

        $data ??= new SortingData();

        return $data;
    }

    private function getInitialFiltrationData(): ?FiltrationData
    {
        if (!$this->config->isFiltrationEnabled()) {
            return null;
        }

        $data = null;

        if ($this->config->isFiltrationPersistenceEnabled()) {
            $data = $this->getPersistenceData(PersistenceContext::Filtration);
        }

        $data ??= $this->config->getDefaultFiltrationData();

        $data ??= FiltrationData::fromDataTable($this);

        $data->appendMissingFilters($this->getFilters());

        return $data;
    }

    private function getInitialPersonalizationData(): ?PersonalizationData
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return null;
        }

        $data = null;

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            $data = $this->getPersistenceData(PersistenceContext::Personalization);
        }

        $data ??= $this->config->getDefaultPersonalizationData();

        $data ??= PersonalizationData::fromDataTable($this);

        return $data;
    }

    private function isPersistenceEnabled(PersistenceContext $context): bool
    {
        return match ($context) {
            PersistenceContext::Sorting => $this->config->isSortingPersistenceEnabled(),
            PersistenceContext::Pagination => $this->config->isPaginationPersistenceEnabled(),
            PersistenceContext::Filtration => $this->config->isFiltrationPersistenceEnabled(),
            PersistenceContext::Personalization => $this->config->isPersonalizationPersistenceEnabled(),
        };
    }

    private function getPersistenceData(PersistenceContext $context): mixed
    {
        if (!$this->isPersistenceEnabled($context)) {
            throw new RuntimeException(sprintf('The data table has %s persistence disabled.', $context->value));
        }

        $persistenceAdapter = $this->getPersistenceAdapter($context);
        $persistenceSubject = $this->getPersistenceSubject($context);

        return $persistenceAdapter->read($this, $persistenceSubject);
    }

    private function setPersistenceData(PersistenceContext $context, mixed $data): void
    {
        if (!$this->isPersistenceEnabled($context)) {
            throw new RuntimeException(sprintf('The data table has %s persistence disabled.', $context->value));
        }

        $persistenceAdapter = $this->getPersistenceAdapter($context);
        $persistenceSubject = $this->getPersistenceSubject($context);

        $persistenceAdapter->write($this, $persistenceSubject, $data);
    }

    private function getPersistenceAdapter(PersistenceContext $context): PersistenceAdapterInterface
    {
        $adapter = match ($context) {
            PersistenceContext::Sorting => $this->config->getSortingPersistenceAdapter(),
            PersistenceContext::Pagination => $this->config->getPaginationPersistenceAdapter(),
            PersistenceContext::Filtration => $this->config->getFiltrationPersistenceAdapter(),
            PersistenceContext::Personalization => $this->config->getPersonalizationPersistenceAdapter(),
        };

        if (null === $adapter) {
            throw new RuntimeException(sprintf('The data table is configured to use %s persistence, but does not have an adapter.', $context->value));
        }

        return $adapter;
    }

    /**
     * @throws Persistence\PersistenceSubjectNotFoundException
     */
    private function getPersistenceSubject(PersistenceContext $context): PersistenceSubjectInterface
    {
        $provider = match ($context) {
            PersistenceContext::Sorting => $this->config->getSortingPersistenceSubjectProvider(),
            PersistenceContext::Pagination => $this->config->getPaginationPersistenceSubjectProvider(),
            PersistenceContext::Filtration => $this->config->getFiltrationPersistenceSubjectProvider(),
            PersistenceContext::Personalization => $this->config->getPersonalizationPersistenceSubjectProvider(),
        };

        if (null === $provider) {
            throw new RuntimeException(sprintf('The data table is configured to use %s persistence, but does not have a subject provider.', $context->value));
        }

        return $provider->provide();
    }

    private function resetPersonalization(): void
    {
        $this->personalizationData = null;

        foreach ($this->columns as $column) {
            $column
                ->setPriority($column->getConfig()->getOption('priority'))
                ->setVisible($column->getConfig()->getOption('visible'))
            ;
        }
    }
}

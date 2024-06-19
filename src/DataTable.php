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
use Kreyu\Bundle\DataTableBundle\Pagination\CurrentPageOutOfRangeException;
use Kreyu\Bundle\DataTableBundle\Pagination\Pagination;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceContext;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Form\Type\PersonalizationDataType;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ResultSetInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Traversable;

class DataTable implements DataTableInterface
{
    /**
     * @var array<string, ColumnInterface>
     */
    private array $columns = [];

    /**
     * @var array<string, FilterInterface>
     */
    private array $filters = [];

    /**
     * @var array<string, ActionInterface>
     */
    private array $actions = [];

    private ?SortingData $sortingData = null;

    private ?PaginationData $paginationData = null;

    private ?FiltrationData $filtrationData = null;

    private ?PersonalizationData $personalizationData = null;

    private ?ExportData $exportData = null;

    private ?FormInterface $filtrationForm = null;

    private ?FormInterface $personalizationForm = null;

    private ?FormInterface $exportForm = null;

    private ?PaginationInterface $pagination = null;

    private ?ResultSetInterface $resultSet = null;

    private ProxyQueryInterface $queryWithoutFilters;

    private bool $initialized = false;

    public function __construct(
        private ProxyQueryInterface $query,
        private /* readonly */ DataTableConfigInterface $config,
    ) {
        $this->queryWithoutFilters = clone $this->query;
    }

    public function __clone(): void
    {
        $this->config = clone $this->config;
        $this->query = clone $this->query;
    }

    public function getIterator(): Traversable
    {
        return $this->getResultSet()->getIterator();
    }

    public function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        if (null === $this->paginationData && $paginationData = $this->getInitialPaginationData()) {
            $this->paginate($paginationData);
        }

        if (null === $this->sortingData && $sortingData = $this->getInitialSortingData()) {
            $this->sort($sortingData);
        }

        if (null === $this->filtrationData && $filtrationData = $this->getInitialFiltrationData()) {
            $this->filter($filtrationData);
        }

        if (null === $this->personalizationData && $personalizationData = $this->getInitialPersonalizationData()) {
            $this->personalize($personalizationData);
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

        uasort($columns, static function (ColumnInterface $columnA, ColumnInterface $columnB): int {
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
        return isset($this->columns[$name]);
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

        $this->personalizationForm = null;

        return $this;
    }

    public function removeColumn(string $name): static
    {
        unset($this->columns[$name]);

        $this->personalizationForm = null;

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
        return isset($this->filters[$name]);
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

        $this->filtrationForm = null;

        return $this;
    }

    public function removeFilter(string $name): static
    {
        unset($this->filters[$name]);

        $this->filtrationForm = null;

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
        return isset($this->actions[$name]);
    }

    public function addAction(ActionInterface|string $action, string $type = ActionType::class, array $options = []): static
    {
        if (is_string($action)) {
            $action = $this->config->getActionFactory()
                ->createNamedBuilder($action, $type, $options)
                ->setContext(ActionContext::Global)
                ->getAction()
            ;
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
        return isset($this->batchActions[$name]);
    }

    public function addBatchAction(ActionInterface|string $action, string $type = ActionType::class, array $options = []): static
    {
        if (is_string($action)) {
            $action = $this->config->getActionFactory()
                ->createNamedBuilder($action, $type, $options)
                ->setContext(ActionContext::Batch)
                ->getAction()
            ;
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
        return isset($this->rowActions[$name]);
    }

    public function addRowAction(ActionInterface|string $action, string $type = ActionType::class, array $options = []): static
    {
        if (is_string($action)) {
            $action = $this->config->getActionFactory()
                ->createNamedBuilder($action, $type, $options)
                ->setContext(ActionContext::Row)
                ->getAction()
            ;
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
        return isset($this->exporters[$name]);
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

    public function paginate(PaginationData $data): void
    {
        if (!$this->config->isPaginationEnabled()) {
            return;
        }

        $this->resultSet = null;

        $this->dispatch(DataTableEvents::PRE_PAGINATE, $event = new DataTablePaginationEvent($this, $data));

        $data = $event->getData();

        $this->query->paginate($data);

        $this->setPaginationData($data);

        $this->dispatch(DataTableEvents::POST_PAGINATE, new DataTablePaginationEvent($this, $data));
    }

    public function sort(SortingData $data): void
    {
        if (!$this->config->isSortingEnabled()) {
            return;
        }

        $this->resultSet = null;

        $this->dispatch(DataTableEvents::PRE_SORT, $event = new DataTableSortingEvent($this, $data));

        $data = $event->getData();

        $this->query->sort($data);

        $this->queryWithoutFilters = clone $this->query;

        $this->setSortingData($data);

        $this->dispatch(DataTableEvents::POST_SORT, new DataTableSortingEvent($this, $data));
    }

    public function filter(FiltrationData $data): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        $this->resultSet = null;

        $this->query = clone $this->queryWithoutFilters;

        $this->dispatch(DataTableEvents::PRE_FILTER, $event = new DataTableFiltrationEvent($this, $data));

        $data = $event->getData();

        foreach ($data as $filterName => $filterData) {
            if ($filterData->hasValue() && $this->hasFilter($filterName)) {
                $this->getFilter($filterName)->handle($this->query, $filterData);
            }
        }

        $this->setFiltrationData($data);

        $this->dispatch(DataTableEvents::POST_FILTER, new DataTableFiltrationEvent($this, $data));
    }

    public function personalize(PersonalizationData $data): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        $this->dispatch(DataTableEvents::PRE_PERSONALIZE, $event = new DataTablePersonalizationEvent($this, $data));

        $data = $event->getData();

        foreach ($data->getColumns() as $columnData) {
            if (!$this->hasColumn($columnData->getName())) {
                continue;
            }

            $column = $this->getColumn($columnData->getName());

            if (!$column->getConfig()->isPersonalizable()) {
                continue;
            }

            $column
                ->setPriority($columnData->getPriority())
                ->setVisible($columnData->isVisible());
        }

        $this->setPersonalizationData($data);

        $this->dispatch(DataTableEvents::POST_PERSONALIZE, new DataTablePersonalizationEvent($this, $data));
    }

    public function export(?ExportData $data = null): ExportFile
    {
        if (!$this->config->isExportingEnabled()) {
            throw new RuntimeException('The data table has exporting feature disabled.');
        }

        $dataTable = clone $this;

        $data ??= $this->exportData ?? $this->config->getDefaultExportData() ?? ExportData::fromDataTable($this);

        $this->dispatch(DataTableEvents::PRE_EXPORT, $event = new DataTableExportEvent($dataTable, $data));

        $data = $event->getData();

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

    public function getPagination(): PaginationInterface
    {
        if (!$this->config->isPaginationEnabled()) {
            throw new RuntimeException('The data table has pagination feature disabled.');
        }

        return $this->pagination ??= $this->createPagination();
    }

    private function createPagination(): PaginationInterface
    {
        $resultSet = $this->getResultSet();

        try {
            return new Pagination(
                currentPageNumber: $this->paginationData?->getPage() ?? 1,
                currentPageItemCount: $resultSet->getCurrentPageItemCount(),
                totalItemCount: $resultSet->getTotalItemCount(),
                itemNumberPerPage: $this->paginationData?->getPerPage(),
            );
        } catch (CurrentPageOutOfRangeException) {
            $this->paginate($this->paginationData->withPage(1));

            return $this->createPagination();
        }
    }

    public function getItems(): iterable
    {
        return $this->getIterator();
    }

    private function getResultSet(): ResultSetInterface
    {
        if (!$this->initialized) {
            $this->initialize();
        }

        return $this->resultSet ??= $this->query->getResult();
    }

    public function getSortingData(): ?SortingData
    {
        return $this->sortingData;
    }

    private function setSortingData(SortingData $data): void
    {
        $this->sortingData = $data;

        if ($this->config->isSortingPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Sorting, $data);
        }
    }

    public function getPaginationData(): ?PaginationData
    {
        return $this->paginationData;
    }

    private function setPaginationData(PaginationData $data): void
    {
        $this->paginationData = $data;

        if ($this->config->isPaginationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Pagination, $data);
        }
    }

    public function getFiltrationData(): ?FiltrationData
    {
        return $this->filtrationData;
    }

    private function setFiltrationData(FiltrationData $data): void
    {
        $this->filtrationData = $data;

        if ($this->config->isFiltrationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Filtration, $data);
        }
    }

    public function getPersonalizationData(): ?PersonalizationData
    {
        return $this->personalizationData;
    }

    private function setPersonalizationData(PersonalizationData $data): void
    {
        $this->personalizationData = $data;

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Personalization, $data);
        }
    }

    public function getExportData(): ?ExportData
    {
        return $this->exportData;
    }

    public function getFiltrationForm(): FormInterface
    {
        if (!$this->config->isFiltrationEnabled()) {
            throw new RuntimeException('The data table has filtration feature disabled.');
        }

        return $this->filtrationForm ??= $this->config->getFiltrationFormFactory()->createNamed(
            name: $this->config->getFiltrationParameterName(),
            type: FiltrationDataType::class,
            options: [
                'filters' => $this->filters,
            ],
        );
    }

    public function getPersonalizationForm(): FormInterface
    {
        if (!$this->config->isPersonalizationEnabled()) {
            throw new RuntimeException('The data table has personalization feature disabled.');
        }

        return $this->personalizationForm ??= $this->config->getPersonalizationFormFactory()->createNamed(
            name: $this->config->getPersonalizationParameterName(),
            type: PersonalizationDataType::class,
            options: [
                'columns' => $this->columns,
            ],
        );
    }

    public function createExportFormBuilder(?DataTableView $view = null): FormBuilderInterface
    {
        if (!$this->config->isExportingEnabled()) {
            throw new RuntimeException('The data table has export feature disabled.');
        }

        $data = $this->config->getDefaultExportData() ?? ExportData::fromDataTable($this);

        if (null !== $data) {
            $data->filename ??= $this->getName();
        }

        return $this->config->getExportFormFactory()->createNamedBuilder(
            name: $this->config->getExportParameterName(),
            type: ExportDataType::class,
            data: $data,
            options: [
                'exporters' => $this->getExporters(),
            ],
        );
    }

    public function getExportForm(): FormInterface
    {
        if (!$this->config->isExportingEnabled()) {
            throw new RuntimeException('The data table has export feature disabled.');
        }

        return $this->exportForm ??= $this->createExportFormBuilder()->getForm();
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

    private function getInitialPaginationData(): ?PaginationData
    {
        if (!$this->config->isPaginationEnabled()) {
            return null;
        }

        $data = null;

        if ($this->config->isPaginationPersistenceEnabled()) {
            $data = $this->getPersistenceData(PersistenceContext::Pagination);
        }

        return $data ?? $this->config->getDefaultPaginationData() ?? new PaginationData();
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

        return $data ?? $this->config->getDefaultSortingData();
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

        return $data ?? $this->config->getDefaultFiltrationData();
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

        return $data ?? $this->config->getDefaultPersonalizationData();
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

        try {
            $persistenceSubject = $this->getPersistenceSubject($context);
        } catch (Persistence\PersistenceSubjectNotFoundException) {
            return null;
        }

        return $persistenceAdapter->read($this, $persistenceSubject);
    }

    private function setPersistenceData(PersistenceContext $context, mixed $data): void
    {
        if (!$this->isPersistenceEnabled($context)) {
            throw new RuntimeException(sprintf('The data table has %s persistence disabled.', $context->value));
        }

        $persistenceAdapter = $this->getPersistenceAdapter($context);

        try {
            $persistenceSubject = $this->getPersistenceSubject($context);
        } catch (Persistence\PersistenceSubjectNotFoundException) {
            return;
        }

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

    private function dispatch(string $eventName, DataTableEvent $event): void
    {
        $dispatcher = $this->config->getEventDispatcher();

        if ($dispatcher->hasListeners($eventName)) {
            $dispatcher->dispatch($event, $eventName);
        }
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

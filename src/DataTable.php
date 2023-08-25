<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
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
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\Form\Type\PersonalizationDataType;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingColumnData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

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

    // TODO: Handle nullable query
    public function __construct(
        private ProxyQueryInterface $query,
        private /*readonly*/ DataTableConfigInterface $config,
    ) {
        $this->originalQuery = clone $this->query;
    }

    public function __clone(): void
    {
        $this->config = clone $this->config;
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
        return $this->columns;
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

        $this->query->paginate($data);

        $this->originalQuery = $this->query;

        $this->paginationData = $data;

        if ($persistence && $this->config->isPaginationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Pagination, $data);
        }

        $this->resetPagination();
    }

    public function sort(SortingData $data, bool $persistence = true): void
    {
        if (!$this->config->isSortingEnabled()) {
            return;
        }

        $columns = $this->getColumns();

        $data->removeRedundantColumns($columns);

        $sortingDataFiltered = new SortingData();

        foreach ($data->getColumns() as $sortingColumnData) {
            try {
                $column = $this->getColumn($sortingColumnData->getName());
            } catch (\Throwable) {
                continue;
            }

            $sortingDataFiltered->addColumn($column->getName(), SortingColumnData::fromArray([
                'name' => (string) $column->getSortPropertyPath(),
                'direction' => $sortingColumnData->getDirection(),
            ]));
        }

        $this->query->sort($sortingDataFiltered);

        $this->originalQuery = $this->query;

        if ($persistence && $this->config->isSortingPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Sorting, $data);
        }

        $this->setSortingData($data);
        $this->resetPagination();
    }

    public function filter(FiltrationData $data, bool $persistence = true): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        $this->query = clone $this->originalQuery;

        $filters = $this->getFilters();

        foreach ($filters as $filter) {
            $filterData = $data->getFilterData($filter->getName());

            if ($filterData && $filterData->hasValue()) {
                $filter->apply($this->query, $filterData);
            }
        }

        $data->appendMissingFilters($filters);
        $data->removeRedundantFilters($filters);

        if ($persistence && $this->config->isFiltrationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Filtration, $data);
        }

        $this->setFiltrationData($data);
        $this->resetPagination();
    }

    public function personalize(PersonalizationData $data, bool $persistence = true): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        $columns = $this->getColumns();

        $data->addMissingColumns($columns);
        $data->removeRedundantColumns($columns);

        if ($persistence && $this->config->isPersonalizationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Personalization, $data);
        }

        $this->setPersonalizationData($data);
    }

    public function export(ExportData $data = null): ExportFile
    {
        if (!$this->config->isExportingEnabled()) {
            throw new RuntimeException('The data table has exporting feature disabled.');
        }

        $data ??= $this->exportData ?? $this->config->getDefaultExportData() ?? ExportData::fromDataTable($this);

        $dataTable = clone $this;

        if (ExportStrategy::IncludeAll === $data->strategy) {
            $dataTable->getQuery()->paginate(new PaginationData(perPage: null));
        }

        if (!$data->includePersonalization) {
            $dataTable->personalizationData = null;
        }

        return $data->exporter->export($dataTable->createExportView(), $data->filename);
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

        if (null === $this->config->getFiltrationFormFactory()) {
            throw new RuntimeException('The data table has no configured filtration form factory.');
        }

        return $this->config->getFiltrationFormFactory()->createNamedBuilder(
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

        if (null === $this->config->getPersonalizationFormFactory()) {
            throw new RuntimeException('The data table has no configured personalization form factory.');
        }

        return $this->config->getFiltrationFormFactory()->createNamedBuilder(
            name: $this->config->getPersonalizationParameterName(),
            type: PersonalizationDataType::class,
            options: [
                'data_table_view' => $view,
            ],
        );
    }

    public function getPersonalizationForm(): FormInterface
    {
        return $this->personalizationForm ??= $this->createPersonalizationFormBuilder()->getForm();
    }

    public function createExportFormBuilder(DataTableView $view = null): FormBuilderInterface
    {
        if (!$this->config->isExportingEnabled()) {
            throw new RuntimeException('The data table has export feature disabled.');
        }

        if (null === $this->config->getExportFormFactory()) {
            throw new RuntimeException('The data table has no configured export form factory.');
        }

        return $this->config->getExportFormFactory()->createNamedBuilder(
            name: $this->config->getExportParameterName(),
            type: ExportDataType::class,
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
        if (empty($this->getColumns())) {
            throw new RuntimeException('The data table has no configured columns.');
        }

        $type = $this->config->getType();
        $options = $this->config->getOptions();

        $view = $type->createView($this);

        $type->buildView($view, $this, $options);

        return $view;
    }

    public function createExportView(): DataTableView
    {
        if (empty($this->getColumns())) {
            throw new RuntimeException('The data table has no configured columns.');
        }

        $type = $this->config->getType();
        $options = $this->config->getOptions();

        $view = $type->createExportView($this);

        $type->buildExportView($view, $this, $options);

        return $view;
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
}

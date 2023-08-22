<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Exception\OutOfBoundsException;
use Kreyu\Bundle\DataTableBundle\Exception\RuntimeException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportStrategy;
use Kreyu\Bundle\DataTableBundle\Exporter\Form\Type\ExportDataType;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\FiltrationDataType;
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
    private ProxyQueryInterface $nonFilteredQuery;

    public function __construct(
        private ProxyQueryInterface $query,
        private DataTableConfigInterface $config,
    ) {
        $this->nonFilteredQuery = clone $this->query;
    }

    public function __clone(): void
    {
        $this->config = clone $this->config;
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

    public function addColumn(ColumnInterface|string $column, string $type = null, array $options = []): static
    {
        if (is_string($column)) {
            $column = $this->getConfig()
                ->getColumnFactory()
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

    public function addAction(ActionInterface|string $action, string $type = null, array $options = []): static
    {
        if (is_string($action)) {
            $builder = $this->getConfig()->getActionFactory()->createNamedBuilder($action, $type, $options);
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

    public function addBatchAction(ActionInterface|string $action, string $type = null, array $options = []): static
    {
        if (is_string($action)) {
            $builder = $this->getConfig()->getActionFactory()->createNamedBuilder($action, $type, $options);
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

    public function addRowAction(ActionInterface|string $action, string $type = null, array $options = []): static
    {
        if (is_string($action)) {
            $builder = $this->getConfig()->getActionFactory()->createNamedBuilder($action, $type, $options);
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

    public function paginate(PaginationData $data): void
    {
        if (!$this->config->isPaginationEnabled()) {
            return;
        }

        $this->query->paginate($data);

        $this->nonFilteredQuery = $this->query;

        $this->paginationData = $data;

        if ($this->config->isPaginationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Pagination, $data);
        }

        $this->pagination = null;
    }

    public function sort(SortingData $data): void
    {
        if (!$this->config->isSortingEnabled()) {
            return;
        }

        $sortingDataFiltered = new SortingData();

        foreach ($data->getColumns() as $sortingColumnData) {
            try {
                $column = $this->getConfig()->getColumn($sortingColumnData->getName());
            } catch (\Throwable) {
                continue;
            }

            $sortField = $column->getOptions()['sort'];

            if (false === $sortField) {
                continue;
            }

            if (true === $sortField) {
                $sortField = $column->getOptions()['property_path'] ?: $column->getName();
            }

            $sortingDataFiltered->addColumn($column->getName(), SortingColumnData::fromArray([
                'name' => $sortField,
                'direction' => $sortingColumnData->getDirection(),
            ]));
        }

        $this->query->sort($sortingDataFiltered);

        $this->nonFilteredQuery = $this->query;

        $this->sortingData = $data;

        if ($this->config->isSortingPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Sorting, $data);
        }

        $this->pagination = null;
    }

    public function filter(FiltrationData $data): void
    {
        if (!$this->config->isFiltrationEnabled()) {
            return;
        }

        $this->query = clone $this->nonFilteredQuery;

        $filters = $this->config->getFilters();

        foreach ($filters as $filter) {
            $filterData = $data->getFilterData($filter->getName());

            if ($filterData && $filterData->hasValue()) {
                $filter->apply($this->query, $filterData);
            }
        }

        $filters = $this->config->getFilters();

        $data->appendMissingFilters($filters);
        $data->removeRedundantFilters($filters);

        $this->filtrationData = $data;

        if ($this->config->isFiltrationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Filtration, $data);
        }

        $this->pagination = null;
    }

    public function personalize(PersonalizationData $data): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        $columns = $this->getColumns();

        $data->addMissingColumns($columns);
        $data->removeRedundantColumns($columns);

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            $this->setPersistenceData(PersistenceContext::Personalization, $data);
        }

        $this->getPersonalizationForm()->setData($data);
        $this->personalizationData = $data;
    }

    public function export(ExportData $data = null): ExportFile
    {
        if (!$this->config->isExportingEnabled()) {
            throw new RuntimeException('The data table has exporting feature disabled.');
        }

        if (null === $data) {
            if ($this->getExportForm()->isSubmitted()) {
                $data = $this->getExportForm()->getData();
            } else {
                $data = $this->getConfig()->getDefaultExportData() ?? ExportData::fromDataTable($this);
            }
        }

        $dataTable = clone $this;

        if (ExportStrategy::INCLUDE_ALL === $data->strategy) {
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

    public function getSortingData(): SortingData
    {
        return $this->sortingData;
    }

    public function getPaginationData(): PaginationData
    {
        return $this->paginationData;
    }

    public function getFiltrationData(): ?FiltrationData
    {
        return $this->filtrationData;
    }

    public function getPersonalizationData(): ?PersonalizationData
    {
        return $this->personalizationData;
    }

    public function getExportData(): ?ExportData
    {
        return $this->getExportForm()->getData();
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
            name: $this->getConfig()->getFiltrationParameterName(),
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
            name: $this->getConfig()->getPersonalizationParameterName(),
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

    public function getExportForm(): FormInterface
    {
        return $this->exportForm ??= $this->createExportFormBuilder()->getForm();
    }

    public function createExportFormBuilder(): FormBuilderInterface
    {
        if (!$this->config->isExportingEnabled()) {
            throw new RuntimeException('The data table has export feature disabled.');
        }

        if (null === $this->config->getExportFormFactory()) {
            throw new RuntimeException('The data table has no configured export form factory.');
        }

        return $this->config->getExportFormFactory()->createNamedBuilder(
            name: $this->getConfig()->getExportParameterName(),
            type: ExportDataType::class,
            options: [
                'exporters' => $this->config->getExporters(),
            ],
        );
    }

    public function isExporting(): bool
    {
        return $this->getExportForm()->isSubmitted();
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
        if (empty($this->config->getColumns())) {
            throw new \LogicException('The data table has no configured columns.');
        }

        $type = $this->config->getType();
        $options = $this->config->getOptions();

        $view = $type->createView($this);

        $type->buildView($view, $this, $options);

        return $view;
    }

    public function createExportView(): DataTableView
    {
        if (empty($this->config->getColumns())) {
            throw new \LogicException('The data table has no configured columns.');
        }

        $type = $this->config->getType();
        $options = $this->config->getOptions();

        $view = $type->createExportView($this);

        $type->buildExportView($view, $this, $options);

        return $view;
    }

    public function initialize(): void
    {
        if ($paginationData = $this->getInitialPaginationData()) {
            $this->paginate($paginationData);
        }

        if ($sortingData = $this->getInitialSortingData()) {
            $this->sort($sortingData);
        }

        if ($filtrationData = $this->getInitialFiltrationData()) {
            $this->filter($filtrationData);
        }

        if ($personalizationData = $this->getInitialPersonalizationData()) {
            $this->personalize($personalizationData);
        }
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

        $data->appendMissingFilters($this->getConfig()->getFilters());

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

    private function getPersistenceSubject(PersistenceContext $context): PersistenceSubjectInterface
    {
        $subject = match ($context) {
            PersistenceContext::Sorting => $this->config->getSortingPersistenceSubject(),
            PersistenceContext::Pagination => $this->config->getPaginationPersistenceSubject(),
            PersistenceContext::Filtration => $this->config->getFiltrationPersistenceSubject(),
            PersistenceContext::Personalization => $this->config->getPersonalizationPersistenceSubject(),
        };

        if (null === $subject) {
            throw new RuntimeException(sprintf('The data table is configured to use %s persistence, but does not have a subject.', $context->value));
        }

        return $subject;
    }
}

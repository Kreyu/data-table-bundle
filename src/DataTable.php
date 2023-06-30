<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Exception\OutOfBoundsException;
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

class DataTable implements DataTableInterface
{
    /**
     * @var array<ActionInterface>
     */
    private array $actions = [];

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

    public function addAction(ActionInterface|string $action, string $type = null, array $options = []): static
    {
        if (is_string($action)) {
            $action = $this->getConfig()->getActionFactory()->createNamed($action, $type, $options);
        }

        $this->actions[$action->getName()] = $action;

        $action->setDataTable($this);

        return $this;
    }

    public function removeColumn(string $name): static
    {
        unset($this->actions[$name]);

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
            $this->setPersistenceData('pagination', $data);
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

            if ($sortField === false) {
                continue;
            }

            if ($sortField === true) {
                $sortField = $column->getOptions()['property_path'] ?? $column->getName();
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
            $this->setPersistenceData('sorting', $data);
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
            $this->setPersistenceData('filtration', $data);
        }

        $this->pagination = null;
    }

    public function personalize(PersonalizationData $data): void
    {
        if (!$this->config->isPersonalizationEnabled()) {
            return;
        }

        $columns = $this->config->getColumns();

        $data->addMissingColumns($columns);
        $data->removeRedundantColumns($columns);

        if ($this->config->isPersonalizationPersistenceEnabled()) {
            $this->setPersistenceData('personalization', $data);
        }

        $this->personalizationData = $data;
    }

    public function export(ExportData $data = null): ExportFile
    {
        if (!$this->config->isExportingEnabled()) {
            throw new \RuntimeException('The data table requested to export has exporting feature disabled.');
        }

        $data ??= $this->exportData ?? $this->getConfig()->getDefaultExportData() ?? ExportData::fromDataTable($this);

        $this->exportData = $data;

        $dataTable = clone $this;

        // TODO: This should be done in a better way...
        if ($dataTable->config instanceof DataTableConfigBuilderInterface) {
            $dataTable->config->setPaginationPersistenceEnabled(false);
            $dataTable->config->setPersonalizationPersistenceEnabled(false);
        }

        if (ExportStrategy::INCLUDE_ALL === $data->strategy) {
            $dataTable->paginate(new PaginationData(perPage: null));
        }

        if (!$data->includePersonalization) {
            $dataTable->personalize(PersonalizationData::fromDataTable($this));
        }

        return $data->exporter->export($dataTable->createView(), $data->filename);
    }

    public function getPagination(): PaginationInterface
    {
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

    public function getPersonalizationData(): PersonalizationData
    {
        return $this->personalizationData;
    }

    public function getExportData(): ?ExportData
    {
        return $this->exportData;
    }

    public function createFiltrationFormBuilder(DataTableView $view = null): FormBuilderInterface
    {
        if (!$this->config->isFiltrationEnabled()) {
            throw new \RuntimeException('The data table has filtration feature disabled.');
        }

        if (null === $this->config->getFiltrationFormFactory()) {
            throw new \RuntimeException('The data table has no configured filtration form factory.');
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
            throw new \RuntimeException('The data table has personalization feature disabled.');
        }

        if (null === $this->config->getPersonalizationFormFactory()) {
            throw new \RuntimeException('The data table has no configured personalization form factory.');
        }

        return $this->config->getFiltrationFormFactory()->createNamedBuilder(
            name: $this->getConfig()->getPersonalizationParameterName(),
            type: PersonalizationDataType::class,
            options: [
                'data_table_view' => $view,
            ],
        );
    }

    public function createExportFormBuilder(): FormBuilderInterface
    {
        if (!$this->config->isExportingEnabled()) {
            throw new \RuntimeException('The data table has export feature disabled.');
        }

        if (null === $this->config->getExportFormFactory()) {
            throw new \RuntimeException('The data table has no configured export form factory.');
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
        return null !== $this->exportData;
    }

    public function hasActiveFilters(): bool
    {
        return (bool) $this->filtrationData?->hasActiveFilters();
    }

    public function handleRequest(mixed $request): void
    {
        if (null === $requestHandler = $this->config->getRequestHandler()) {
            throw new \RuntimeException('The "handleRequest" method cannot be used on data tables without configured request handler.');
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
            $data = $this->getPersistenceData('pagination');
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
            $data = $this->getPersistenceData('sorting');
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
            $data = $this->getPersistenceData('filtration');
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
            $data = $this->getPersistenceData('personalization');
        }

        $data ??= $this->config->getDefaultPersonalizationData();

        $data ??= PersonalizationData::fromDataTable($this);

        return $data;
    }

    private function isPersistenceEnabled(string $context): bool
    {
        return match ($context) {
            'sorting' => $this->config->isSortingPersistenceEnabled(),
            'pagination' => $this->config->isPaginationPersistenceEnabled(),
            'filtration' => $this->config->isFiltrationPersistenceEnabled(),
            'personalization' => $this->config->isPersonalizationPersistenceEnabled(),
            default => throw new \RuntimeException('Given persistence context is not supported.'),
        };
    }

    private function getPersistenceData(string $context): mixed
    {
        if (!$this->isPersistenceEnabled($context)) {
            throw new \RuntimeException(sprintf('The data table has %s persistence disabled.', $context));
        }

        $persistenceAdapter = $this->getPersistenceAdapter($context);
        $persistenceSubject = $this->getPersistenceSubject($context);

        return $persistenceAdapter->read($this, $persistenceSubject);
    }

    private function setPersistenceData(string $context, mixed $data): void
    {
        if (!$this->isPersistenceEnabled($context)) {
            throw new \RuntimeException(sprintf('The data table has %s persistence disabled.', $context));
        }

        $persistenceAdapter = $this->getPersistenceAdapter($context);
        $persistenceSubject = $this->getPersistenceSubject($context);

        $persistenceAdapter->write($this, $persistenceSubject, $data);
    }

    private function getPersistenceAdapter(string $context): PersistenceAdapterInterface
    {
        $adapter = match ($context) {
            'sorting' => $this->config->getSortingPersistenceAdapter(),
            'pagination' => $this->config->getPaginationPersistenceAdapter(),
            'filtration' => $this->config->getFiltrationPersistenceAdapter(),
            'personalization' => $this->config->getPersonalizationPersistenceAdapter(),
            default => throw new \RuntimeException('Given persistence context is not supported.'),
        };

        if (null === $adapter) {
            throw new \RuntimeException(sprintf('The data table is configured to use %s persistence, but does not have an adapter.', $context));
        }

        return $adapter;
    }

    private function getPersistenceSubject(string $context): PersistenceSubjectInterface
    {
        $subject = match ($context) {
            'sorting' => $this->config->getSortingPersistenceSubject(),
            'pagination' => $this->config->getPaginationPersistenceSubject(),
            'filtration' => $this->config->getFiltrationPersistenceSubject(),
            'personalization' => $this->config->getPersonalizationPersistenceSubject(),
            default => throw new \RuntimeException('Given persistence context is not supported.'),
        };

        if (null === $subject) {
            throw new \RuntimeException(sprintf('The data table is configured to use %s persistence, but does not have a subject.', $context));
        }

        return $subject;
    }
}

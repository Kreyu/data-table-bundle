<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;

class DataTableBuilder implements DataTableBuilderInterface
{
    /**
     * Name of the data table, used to differentiate multiple data tables on the same page.
     */
    private string $name;

    /**
     * Resolved type class, containing instructions on how to build a data table.
     */
    private ResolvedDataTableTypeInterface $type;

    /**
     * Query used to retrieve and manipulate source of the data table.
     */
    private null|ProxyQueryInterface $query;

    /**
     * Stores an array of options, used to configure a builder behavior.
     */
    private array $options;

    /**
     * Stores an array of columns, used to display the data table to the user.
     *
     * @var array<ColumnInterface>
     */
    private array $columns = [];

    /**
     * Stores an array of filters, used to build and handle the filtering feature.
     *
     * @var array<FilterInterface>
     */
    private array $filters = [];

    /**
     * Stores an array of exporters, used to output data to various file types.
     *
     * @var array<ExporterInterface>
     */
    private array $exporters = [];

    /**
     * Factory used to create proper column models.
     */
    private ColumnFactoryInterface $columnFactory;

    /**
     * Factory used to create proper filter models.
     */
    private FilterFactoryInterface $filterFactory;

    /**
     * Factory used to create proper exporter models.
     */
    private ExporterFactoryInterface $exporterFactory;

    /**
     * Determines whether the data table exporting feature is enabled.
     */
    private bool $exportingEnabled = true;

    /**
     * Form factory used to create an export form.
     */
    private null|FormFactoryInterface $exportFormFactory = null;

    /**
     * Determines whether the data table personalization feature is enabled.
     */
    private bool $personalizationEnabled = true;

    /**
     * Determines whether the data table personalization persistence feature is enabled.
     */
    private bool $personalizationPersistenceEnabled = true;

    /**
     * Persistence adapter used to read/write personalization feature data.
     */
    private null|PersistenceAdapterInterface $personalizationPersistenceAdapter = null;

    /**
     * Subject (e.g. logged-in user) used to associate with the personalization persistence feature data.
     */
    private null|PersistenceSubjectInterface $personalizationPersistenceSubject = null;

    /**
     * Form factory used to create a personalization form.
     */
    private null|FormFactoryInterface $personalizationFormFactory = null;

    /**
     * Default personalization data, which is applied to the data table if no data is given by the user.
     */
    private null|PersonalizationData $defaultPersonalizationData = null;

    /**
     * Determines whether the data table filtration feature is enabled.
     */
    private bool $filtrationEnabled = true;

    /**
     * Determines whether the data table filtration persistence feature is enabled.
     */
    private bool $filtrationPersistenceEnabled = true;

    /**
     * Persistence adapter used to read/write filtration feature data.
     */
    private null|PersistenceAdapterInterface $filtrationPersistenceAdapter = null;

    /**
     * Subject (e.g. logged-in user) used to associate with the filtration persistence feature data.
     */
    private null|PersistenceSubjectInterface $filtrationPersistenceSubject = null;

    /**
     * Form factory used to create a filtration form.
     */
    private null|FormFactoryInterface $filtrationFormFactory = null;

    /**
     * Default filtration data, which is applied to the data table if no data is given by the user.
     */
    private null|FiltrationData $defaultFiltrationData = null;

    /**
     * Determines whether the data table sorting feature is enabled.
     */
    private bool $sortingEnabled = true;

    /**
     * Determines whether the data table sorting persistence feature is enabled.
     */
    private bool $sortingPersistenceEnabled = true;

    /**
     * Persistence adapter used to read/write sorting feature data.
     */
    private null|PersistenceAdapterInterface $sortingPersistenceAdapter = null;

    /**
     * Subject (e.g. logged-in user) used to associate with the sorting persistence feature data.
     */
    private null|PersistenceSubjectInterface $sortingPersistenceSubject = null;

    /**
     * Default sorting data, which is applied to the data table if no data is given by the user.
     */
    private null|SortingData $defaultSortingData = null;

    /**
     * Determines whether the data table pagination feature is enabled.
     */
    private bool $paginationEnabled = true;

    /**
     * Determines whether the data table pagination persistence feature is enabled.
     */
    private bool $paginationPersistenceEnabled = true;

    /**
     * Persistence adapter used to read/write pagination feature data.
     */
    private null|PersistenceAdapterInterface $paginationPersistenceAdapter = null;

    /**
     * Subject (e.g. logged-in user) used to associate with the pagination persistence feature data.
     */
    private null|PersistenceSubjectInterface $paginationPersistenceSubject = null;

    /**
     * Default pagination data, which is applied to the data table if no data is given by the user.
     */
    private null|PaginationData $defaultPaginationData = null;

    /**
     * Request handler class used to automatically apply data from request to the data table.
     */
    private null|RequestHandlerInterface $requestHandler = null;

    /**
     * Determines whether the builder is locked, therefore no setters can be called.
     */
    private bool $locked = false;

    public function __construct(string $name, ?ProxyQueryInterface $query = null, array $options = [])
    {
        $this->name = $name;
        $this->query = $query;
        $this->options = $options;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->name = $name;

        return $this;
    }

    public function getType(): ResolvedDataTableTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedDataTableTypeInterface $type): void
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->type = $type;
    }

    public function getQuery(): ?ProxyQueryInterface
    {
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

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->options = $options;

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function getColumn(string $name): ColumnInterface
    {
        return $this->columns[$name] ?? throw new \InvalidArgumentException("Column \"$name\" does not exist");
    }

    public function addColumn(string $name, string $type, array $options = []): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->columns[$name] = $this->getColumnFactory()->create($name, $type, $options);

        return $this;
    }

    public function removeColumn(string $name): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        unset($this->columns[$name]);

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getFilter(string $name): FilterInterface
    {
        return $this->filters[$name] ?? throw new \InvalidArgumentException("Filter \"$name\" does not exist");
    }

    public function addFilter(string $name, string $type, array $options = []): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->filters[$name] = $this->getFilterFactory()->create($name, $type, $options);

        return $this;
    }

    public function removeFilter(string $name): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        unset($this->filters[$name]);

        return $this;
    }

    public function getExporters(): array
    {
        return $this->exporters;
    }

    public function getExporter(string $name): ExporterInterface
    {
        return $this->exporters[$name] ?? throw new \InvalidArgumentException("Exporter \"$name\" does not exist");
    }

    public function addExporter(string $name, string $type, array $options = []): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->exporters[$name] = $this->getExporterFactory()->create($name, $type, $options);

        return $this;
    }

    public function removeExporter(string $name): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        unset($this->exporters[$name]);

        return $this;
    }

    public function getColumnFactory(): ColumnFactoryInterface
    {
        return $this->columnFactory;
    }

    public function setColumnFactory(ColumnFactoryInterface $columnFactory): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->columnFactory = $columnFactory;

        return $this;
    }

    public function getFilterFactory(): FilterFactoryInterface
    {
        return $this->filterFactory;
    }

    public function setFilterFactory(FilterFactoryInterface $filterFactory): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->filterFactory = $filterFactory;

        return $this;
    }

    public function getExporterFactory(): ExporterFactoryInterface
    {
        return $this->exporterFactory;
    }

    public function setExporterFactory(ExporterFactoryInterface $exporterFactory): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->exporterFactory = $exporterFactory;

        return $this;
    }

    public function getBatchActionFactory(): BatchActionFactoryInterface
    {
        return $this->batchActionFactory;
    }

    public function setBatchActionFactory(BatchActionFactoryInterface $batchActionFactory): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->batchActionFactory = $batchActionFactory;

        return $this;
    }

    public function isExportingEnabled(): bool
    {
        return $this->exportingEnabled;
    }

    public function setExportingEnabled(bool $exportingEnabled): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->exportingEnabled = $exportingEnabled;

        return $this;
    }

    public function getExportFormFactory(): ?FormFactoryInterface
    {
        return $this->exportFormFactory;
    }

    public function setExportFormFactory(?FormFactoryInterface $exportFormFactory): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->exportFormFactory = $exportFormFactory;

        return $this;
    }

    public function isPersonalizationEnabled(): bool
    {
        return $this->personalizationEnabled;
    }

    public function setPersonalizationEnabled(bool $personalizationEnabled): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->personalizationEnabled = $personalizationEnabled;

        return $this;
    }

    public function isPersonalizationPersistenceEnabled(): bool
    {
        return $this->personalizationPersistenceEnabled;
    }

    public function setPersonalizationPersistenceEnabled(bool $personalizationPersistenceEnabled): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->personalizationPersistenceEnabled = $personalizationPersistenceEnabled;

        return $this;
    }

    public function getPersonalizationPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->personalizationPersistenceAdapter;
    }

    public function setPersonalizationPersistenceAdapter(?PersistenceAdapterInterface $personalizationPersistenceAdapter): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->personalizationPersistenceAdapter = $personalizationPersistenceAdapter;

        return $this;
    }

    public function getPersonalizationPersistenceSubject(): ?PersistenceSubjectInterface
    {
        return $this->personalizationPersistenceSubject;
    }

    public function setPersonalizationPersistenceSubject(?PersistenceSubjectInterface $personalizationPersistenceSubject): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->personalizationPersistenceSubject = $personalizationPersistenceSubject;

        return $this;
    }

    public function getPersonalizationFormFactory(): FormFactoryInterface
    {
        return $this->personalizationFormFactory;
    }

    public function setPersonalizationFormFactory(?FormFactoryInterface $personalizationFormFactory): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->personalizationFormFactory = $personalizationFormFactory;

        return $this;
    }

    public function getDefaultPersonalizationData(): ?PersonalizationData
    {
        return $this->defaultPersonalizationData;
    }

    public function setDefaultPersonalizationData(?PersonalizationData $defaultPersonalizationData): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->defaultPersonalizationData = $defaultPersonalizationData;

        return $this;
    }

    public function isFiltrationEnabled(): bool
    {
        return $this->filtrationEnabled;
    }

    public function setFiltrationEnabled(bool $filtrationEnabled): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->filtrationEnabled = $filtrationEnabled;

        return $this;
    }

    public function isFiltrationPersistenceEnabled(): bool
    {
        return $this->filtrationPersistenceEnabled;
    }

    public function setFiltrationPersistenceEnabled(bool $filtrationPersistenceEnabled): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->filtrationPersistenceEnabled = $filtrationPersistenceEnabled;

        return $this;
    }

    public function getFiltrationPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->filtrationPersistenceAdapter;
    }

    public function setFiltrationPersistenceAdapter(?PersistenceAdapterInterface $filtrationPersistenceAdapter): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->filtrationPersistenceAdapter = $filtrationPersistenceAdapter;

        return $this;
    }

    public function getFiltrationPersistenceSubject(): ?PersistenceSubjectInterface
    {
        return $this->filtrationPersistenceSubject;
    }

    public function setFiltrationPersistenceSubject(?PersistenceSubjectInterface $filtrationPersistenceSubject): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->filtrationPersistenceSubject = $filtrationPersistenceSubject;

        return $this;
    }

    public function getFiltrationFormFactory(): ?FormFactoryInterface
    {
        return $this->filtrationFormFactory;
    }

    public function setFiltrationFormFactory(?FormFactoryInterface $filtrationFormFactory): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->filtrationFormFactory = $filtrationFormFactory;

        return $this;
    }

    public function getDefaultFiltrationData(): ?FiltrationData
    {
        return $this->defaultFiltrationData;
    }

    public function setDefaultFiltrationData(?FiltrationData $defaultFiltrationData): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->defaultFiltrationData = $defaultFiltrationData;

        return $this;
    }

    public function isSortingEnabled(): bool
    {
        return $this->sortingEnabled;
    }

    public function setSortingEnabled(bool $sortingEnabled): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->sortingEnabled = $sortingEnabled;

        return $this;
    }

    public function isSortingPersistenceEnabled(): bool
    {
        return $this->sortingPersistenceEnabled;
    }

    public function setSortingPersistenceEnabled(bool $sortingPersistenceEnabled): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->sortingPersistenceEnabled = $sortingPersistenceEnabled;

        return $this;
    }

    public function getSortingPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->sortingPersistenceAdapter;
    }

    public function setSortingPersistenceAdapter(?PersistenceAdapterInterface $sortingPersistenceAdapter): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->sortingPersistenceAdapter = $sortingPersistenceAdapter;

        return $this;
    }

    public function getSortingPersistenceSubject(): ?PersistenceSubjectInterface
    {
        return $this->sortingPersistenceSubject;
    }

    public function setSortingPersistenceSubject(?PersistenceSubjectInterface $sortingPersistenceSubject): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->sortingPersistenceSubject = $sortingPersistenceSubject;

        return $this;
    }

    public function getDefaultSortingData(): ?SortingData
    {
        return $this->defaultSortingData;
    }

    public function setDefaultSortingData(?SortingData $defaultSortingData): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->defaultSortingData = $defaultSortingData;

        return $this;
    }

    public function isPaginationEnabled(): bool
    {
        return $this->paginationEnabled;
    }

    public function setPaginationEnabled(bool $paginationEnabled): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->paginationEnabled = $paginationEnabled;

        return $this;
    }

    public function isPaginationPersistenceEnabled(): bool
    {
        return $this->paginationPersistenceEnabled;
    }

    public function setPaginationPersistenceEnabled(bool $paginationPersistenceEnabled): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->paginationPersistenceEnabled = $paginationPersistenceEnabled;

        return $this;
    }

    public function getPaginationPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->paginationPersistenceAdapter;
    }

    public function setPaginationPersistenceAdapter(?PersistenceAdapterInterface $paginationPersistenceAdapter): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->paginationPersistenceAdapter = $paginationPersistenceAdapter;

        return $this;
    }

    public function getPaginationPersistenceSubject(): ?PersistenceSubjectInterface
    {
        return $this->paginationPersistenceSubject;
    }

    public function setPaginationPersistenceSubject(?PersistenceSubjectInterface $paginationPersistenceSubject): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->paginationPersistenceSubject = $paginationPersistenceSubject;

        return $this;
    }

    public function getDefaultPaginationData(): ?PaginationData
    {
        return $this->defaultPaginationData;
    }

    public function setDefaultPaginationData(?PaginationData $defaultPaginationData): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->defaultPaginationData = $defaultPaginationData;

        return $this;
    }

    public function getRequestHandler(): ?RequestHandlerInterface
    {
        return $this->requestHandler;
    }

    public function setRequestHandler(?RequestHandlerInterface $requestHandler): static
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        $this->requestHandler = $requestHandler;

        return $this;
    }

    public function getPageParameterName(): string
    {
        return $this->getParameterName(static::PAGE_PARAMETER);
    }

    public function getPerPageParameterName(): string
    {
        return $this->getParameterName(static::PER_PAGE_PARAMETER);
    }

    public function getSortParameterName(): string
    {
        return $this->getParameterName(static::SORT_PARAMETER);
    }

    public function getFiltrationParameterName(): string
    {
        return $this->getParameterName(static::FILTRATION_PARAMETER);
    }

    public function getPersonalizationParameterName(): string
    {
        return $this->getParameterName(static::PERSONALIZATION_PARAMETER);
    }

    public function getExportParameterName(): string
    {
        return $this->getParameterName(static::EXPORT_PARAMETER);
    }

    public function getDataTable(): DataTableInterface
    {
        $this->validate();

        return new DataTable(
            query: clone $this->query,
            config: $this->getDataTableConfig(),
        );
    }

    public function getDataTableConfig(): DataTableConfigInterface
    {
        $config = clone $this;
        $config->locked = true;

        return $config;
    }

    private function validate(): void
    {
        if (null === $this->query) {
            throw new \LogicException('The data table has no proxy query. You must provide it using either the data table factory or the builder "setQuery()" method.');
        }

        if (empty($this->columns)) {
            throw new \LogicException('The data table has no configured columns. You must provide them using the builder "addColumn()" method.');
        }

        $persistenceContexts = [
            'sorting',
            'pagination',
            'filtration',
            'personalization',
        ];

        foreach ($persistenceContexts as $context) {
            if (!$this->{$context . 'Enabled'} || !$this->{$context . 'PersistenceEnabled'}) {
                continue;
            }

            if (null === $this->{$context . 'PersistenceAdapter'}) {
                throw new \LogicException("The data table is configured to use $context persistence, but does not have an adapter.");
            }

            if (null === $this->{$context . 'PersistenceSubject'}) {
                throw new \LogicException("The data table is configured to use $context persistence, but does not have a subject.");
            }
        }
    }

    private function getParameterName(string $prefix): string
    {
        return implode('_', array_filter([$prefix, $this->name]));
    }

    private function createBuilderLockedException(): \BadMethodCallException
    {
        return new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
    }
}

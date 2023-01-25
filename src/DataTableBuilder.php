<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;

class DataTableBuilder implements DataTableBuilderInterface
{
    public const PAGE_PARAMETER = 'page';
    public const PER_PAGE_PARAMETER = 'limit';
    public const SORT_PARAMETER = 'sort';
    public const FILTRATION_PARAMETER = 'filter';
    public const PERSONALIZATION_PARAMETER = 'personalization';

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
     * Factory used to create proper column models.
     */
    private ColumnFactoryInterface $columnFactory;

    /**
     * Factory used to create proper filter models.
     */
    private FilterFactoryInterface $filterFactory;

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
        $this->name = $name;

        return $this;
    }

    public function getType(): ResolvedDataTableTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedDataTableTypeInterface $type): void
    {
        $this->type = $type;
    }

    public function getQuery(): ?ProxyQueryInterface
    {
        return $this->query;
    }

    public function setQuery(?ProxyQueryInterface $query): static
    {
        $this->query = $query;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): static
    {
        $this->options = $options;

        return $this;
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    public function addColumn(string $name, string $type, array $options = []): static
    {
        if ($this->locked) {
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
        }

        $this->columns[$name] = $this->getColumnFactory()->create($name, $type, $options);

        return $this;
    }

    public function removeColumn(string $name): static
    {
        if ($this->locked) {
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
        }

        unset($this->columns[$name]);

        return $this;
    }

    public function getFilters(): array
    {
        return $this->filters;
    }

    public function addFilter(string $name, string $type, array $options = []): static
    {
        if ($this->locked) {
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
        }

        $this->filters[$name] = $this->getFilterFactory()->create($name, $type, $options);

        return $this;
    }

    public function removeFilter(string $name): static
    {
        if ($this->locked) {
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
        }

        unset($this->filters[$name]);

        return $this;
    }

    public function getColumnFactory(): ColumnFactoryInterface
    {
        return $this->columnFactory;
    }

    public function setColumnFactory(ColumnFactoryInterface $columnFactory): static
    {
        $this->columnFactory = $columnFactory;

        return $this;
    }

    public function getFilterFactory(): FilterFactoryInterface
    {
        return $this->filterFactory;
    }

    public function setFilterFactory(FilterFactoryInterface $filterFactory): static
    {
        $this->filterFactory = $filterFactory;

        return $this;
    }

    public function isPersonalizationEnabled(): bool
    {
        return $this->personalizationEnabled;
    }

    public function setPersonalizationEnabled(bool $personalizationEnabled): static
    {
        if ($this->locked) {
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
        $this->personalizationFormFactory = $personalizationFormFactory;

        return $this;
    }

    public function isFiltrationEnabled(): bool
    {
        return $this->filtrationEnabled;
    }

    public function setFiltrationEnabled(bool $filtrationEnabled): static
    {
        if ($this->locked) {
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
        $this->filtrationFormFactory = $filtrationFormFactory;

        return $this;
    }

    public function isSortingEnabled(): bool
    {
        return $this->sortingEnabled;
    }

    public function setSortingEnabled(bool $sortingEnabled): static
    {
        if ($this->locked) {
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
        }

        $this->sortingPersistenceSubject = $sortingPersistenceSubject;

        return $this;
    }

    public function isPaginationEnabled(): bool
    {
        return $this->paginationEnabled;
    }

    public function setPaginationEnabled(bool $paginationEnabled): static
    {
        if ($this->locked) {
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
        }

        $this->paginationPersistenceSubject = $paginationPersistenceSubject;

        return $this;
    }

    public function getRequestHandler(): ?RequestHandlerInterface
    {
        return $this->requestHandler;
    }

    public function setRequestHandler(?RequestHandlerInterface $requestHandler): static
    {
        if ($this->locked) {
            throw new \BadMethodCallException('DataTableConfigBuilder methods cannot be accessed anymore once the builder is turned into a DataTableConfigInterface instance.');
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

    public function getDataTable(): DataTableInterface
    {
        return new DataTable(
            query: $this->query,
            config: $this->getDataTableConfig(),
        );
    }

    public function getDataTableConfig(): DataTableConfigInterface
    {
        $config = clone $this;
        $config->locked = true;

        return $config;
    }

    private function getParameterName(string $prefix): string
    {
        return implode('_', array_filter([$prefix, $this->name]));
    }
}

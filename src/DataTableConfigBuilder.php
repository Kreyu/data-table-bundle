<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

class DataTableConfigBuilder implements DataTableConfigBuilderInterface
{
    use LockableBuilderTrait;

    private bool $paginationEnabled = false;
    private bool $sortingEnabled = false;
    private bool $filtrationEnabled = false;
    private bool $personalizationEnabled = false;
    private bool $exportingEnabled = false;

    private bool $paginationPersistenceEnabled = false;
    private bool $sortingPersistenceEnabled = false;
    private bool $filtrationPersistenceEnabled = false;
    private bool $personalizationPersistenceEnabled = false;

    private ?PersistenceAdapterInterface $paginationPersistenceAdapter = null;
    private ?PersistenceAdapterInterface $sortingPersistenceAdapter = null;
    private ?PersistenceAdapterInterface $filtrationPersistenceAdapter = null;
    private ?PersistenceAdapterInterface $personalizationPersistenceAdapter = null;

    private ?PersistenceSubjectProviderInterface $paginationPersistenceSubjectProvider = null;
    private ?PersistenceSubjectProviderInterface $sortingPersistenceSubjectProvider = null;
    private ?PersistenceSubjectProviderInterface $filtrationPersistenceSubjectProvider = null;
    private ?PersistenceSubjectProviderInterface $personalizationPersistenceSubjectProvider = null;

    private ?FormFactoryInterface $filtrationFormFactory = null;
    private ?FormFactoryInterface $personalizationFormFactory = null;
    private ?FormFactoryInterface $exportFormFactory = null;

    private ?PaginationData $defaultPaginationData = null;
    private ?SortingData $defaultSortingData = null;
    private ?FiltrationData $defaultFiltrationData = null;
    private ?PersonalizationData $defaultPersonalizationData = null;
    private ?ExportData $defaultExportData = null;

    private ?ColumnFactoryInterface $columnFactory = null;
    private ?FilterFactoryInterface $filterFactory = null;
    private ?ActionFactoryInterface $actionFactory = null;
    private ?ExporterFactoryInterface $exporterFactory = null;
    private ?RequestHandlerInterface $requestHandler = null;
    private ?PaginationFactoryInterface $paginationFactory = null;

    /**
     * @var string|callable|null
     */
    private mixed $groupBy = null;

    private bool $sortingClearable = false;

    /**
     * @var array<string, string|TranslatableInterface>
     */
    private array $columnVisibilityGroups = [];

    private array $themes = [];
    private array $attributes = [];
    private array $headerRowAttributes = [];
    private array $valueRowAttributes = [];

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        /* readonly */ private string $name,
        private ResolvedDataTableTypeInterface $type,
        private EventDispatcherInterface $dispatcher,
        /* readonly */ private array $options = [],
    ) {}

    public function addEventListener(string $eventName, callable $listener, int $priority = 0): static
    {
        $this->dispatcher->addListener($eventName, $listener, $priority);

        return $this;
    }

    public function addEventSubscriber(EventSubscriberInterface $subscriber): static
    {
        $this->dispatcher->addSubscriber($subscriber);

        return $this;
    }

    public function getEventDispatcher(): EventDispatcherInterface
    {
        if (!$this->dispatcher instanceof ImmutableEventDispatcher) {
            $this->dispatcher = new ImmutableEventDispatcher($this->dispatcher);
        }

        return $this->dispatcher;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->ensureNotLocked();

        $this->name = $name;

        return $this;
    }

    public function getType(): ResolvedDataTableTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedDataTableTypeInterface $type): static
    {
        $this->ensureNotLocked();

        $this->type = $type;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function hasOption(string $name): bool
    {
        return array_key_exists($name, $this->options);
    }

    public function getOption(string $name, mixed $default = null): mixed
    {
        return array_key_exists($name, $this->options) ? $this->options[$name] : $default;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): static
    {
        $this->ensureNotLocked();

        $this->options = $options;

        return $this;
    }

    public function setOption(string $name, mixed $value): static
    {
        $this->ensureNotLocked();

        $this->options[$name] = $value;

        return $this;
    }

    public function getColumnFactory(): ColumnFactoryInterface
    {
        if ($this->columnFactory === null) {
            throw new BadMethodCallException(sprintf(
                'The column factory is not set, use the "%s::setColumnFactory()" method to set the column factory.',
                $this::class,
            ));
        }

        return $this->columnFactory;
    }

    public function setColumnFactory(ColumnFactoryInterface $columnFactory): static
    {
        $this->ensureNotLocked();

        $this->columnFactory = $columnFactory;

        return $this;
    }

    public function getFilterFactory(): FilterFactoryInterface
    {
        if ($this->filterFactory === null) {
            throw new BadMethodCallException(sprintf(
                'The filter factory is not set, use the "%s::setFilterFactory()" method to set the filter factory.',
                $this::class,
            ));
        }

        return $this->filterFactory;
    }

    public function setFilterFactory(FilterFactoryInterface $filterFactory): static
    {
        $this->ensureNotLocked();

        $this->filterFactory = $filterFactory;

        return $this;
    }

    public function getActionFactory(): ActionFactoryInterface
    {
        if ($this->actionFactory === null) {
            throw new BadMethodCallException(sprintf(
                'The action factory is not set, use the "%s::setActionFactory()" method to set the action factory.',
                $this::class,
            ));
        }

        return $this->actionFactory;
    }

    public function setActionFactory(ActionFactoryInterface $actionFactory): static
    {
        $this->ensureNotLocked();

        $this->actionFactory = $actionFactory;

        return $this;
    }

    public function getExporterFactory(): ExporterFactoryInterface
    {
        if ($this->exporterFactory === null) {
            throw new BadMethodCallException(sprintf(
                'The exporter factory is not set, use the "%s::setExporterFactory()" method to set the exporter factory.',
                $this::class,
            ));
        }

        return $this->exporterFactory;
    }

    public function setExporterFactory(ExporterFactoryInterface $exporterFactory): static
    {
        $this->ensureNotLocked();

        $this->exporterFactory = $exporterFactory;

        return $this;
    }

    public function isExportingEnabled(): bool
    {
        return $this->exportingEnabled;
    }

    public function setExportingEnabled(bool $exportingEnabled): static
    {
        $this->ensureNotLocked();

        $this->exportingEnabled = $exportingEnabled;

        return $this;
    }

    public function getExportFormFactory(): ?FormFactoryInterface
    {
        return $this->exportFormFactory;
    }

    public function setExportFormFactory(?FormFactoryInterface $exportFormFactory): static
    {
        $this->ensureNotLocked();

        $this->exportFormFactory = $exportFormFactory;

        return $this;
    }

    public function getDefaultExportData(): ?ExportData
    {
        return $this->defaultExportData;
    }

    public function setDefaultExportData(?ExportData $defaultExportData): static
    {
        $this->ensureNotLocked();

        $this->defaultExportData = $defaultExportData;

        return $this;
    }

    public function isPersonalizationEnabled(): bool
    {
        return $this->personalizationEnabled;
    }

    public function setPersonalizationEnabled(bool $personalizationEnabled): static
    {
        $this->ensureNotLocked();

        $this->personalizationEnabled = $personalizationEnabled;

        return $this;
    }

    public function isPersonalizationPersistenceEnabled(): bool
    {
        return $this->personalizationPersistenceEnabled;
    }

    public function setPersonalizationPersistenceEnabled(bool $personalizationPersistenceEnabled): static
    {
        $this->ensureNotLocked();

        $this->personalizationPersistenceEnabled = $personalizationPersistenceEnabled;

        return $this;
    }

    public function getPersonalizationPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->personalizationPersistenceAdapter;
    }

    public function setPersonalizationPersistenceAdapter(?PersistenceAdapterInterface $personalizationPersistenceAdapter): static
    {
        $this->ensureNotLocked();

        $this->personalizationPersistenceAdapter = $personalizationPersistenceAdapter;

        return $this;
    }

    public function getPersonalizationPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface
    {
        return $this->personalizationPersistenceSubjectProvider;
    }

    public function setPersonalizationPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $personalizationPersistenceSubjectProvider): static
    {
        $this->ensureNotLocked();

        $this->personalizationPersistenceSubjectProvider = $personalizationPersistenceSubjectProvider;

        return $this;
    }

    public function getPersonalizationFormFactory(): FormFactoryInterface
    {
        if ($this->personalizationFormFactory === null) {
            throw new BadMethodCallException('The personalization form factory must be set before retrieving it.');
        }

        return $this->personalizationFormFactory;
    }

    public function setPersonalizationFormFactory(?FormFactoryInterface $personalizationFormFactory): static
    {
        $this->ensureNotLocked();

        $this->personalizationFormFactory = $personalizationFormFactory;

        return $this;
    }

    public function getDefaultPersonalizationData(): ?PersonalizationData
    {
        return $this->defaultPersonalizationData;
    }

    public function setDefaultPersonalizationData(?PersonalizationData $defaultPersonalizationData): static
    {
        $this->ensureNotLocked();

        $this->defaultPersonalizationData = $defaultPersonalizationData;

        return $this;
    }

    public function getColumnVisibilityGroups(): array
    {
        return $this->columnVisibilityGroups;
    }

    public function setColumnVisibilityGroups(array $columnVisibilityGroups): static
    {
        $this->ensureNotLocked();

        $this->columnVisibilityGroups = $columnVisibilityGroups;

        return $this;
    }

    public function isFiltrationEnabled(): bool
    {
        return $this->filtrationEnabled;
    }

    public function setFiltrationEnabled(bool $filtrationEnabled): static
    {
        $this->ensureNotLocked();

        $this->filtrationEnabled = $filtrationEnabled;

        return $this;
    }

    public function isFiltrationPersistenceEnabled(): bool
    {
        return $this->filtrationPersistenceEnabled;
    }

    public function setFiltrationPersistenceEnabled(bool $filtrationPersistenceEnabled): static
    {
        $this->ensureNotLocked();

        $this->filtrationPersistenceEnabled = $filtrationPersistenceEnabled;

        return $this;
    }

    public function getFiltrationPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->filtrationPersistenceAdapter;
    }

    public function setFiltrationPersistenceAdapter(?PersistenceAdapterInterface $filtrationPersistenceAdapter): static
    {
        $this->ensureNotLocked();

        $this->filtrationPersistenceAdapter = $filtrationPersistenceAdapter;

        return $this;
    }

    public function getFiltrationPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface
    {
        return $this->filtrationPersistenceSubjectProvider;
    }

    public function setFiltrationPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $filtrationPersistenceSubjectProvider): static
    {
        $this->ensureNotLocked();

        $this->filtrationPersistenceSubjectProvider = $filtrationPersistenceSubjectProvider;

        return $this;
    }

    public function getFiltrationFormFactory(): FormFactoryInterface
    {
        if ($this->filtrationFormFactory === null) {
            throw new BadMethodCallException('The filtration form factory must be set before retrieving it.');
        }

        return $this->filtrationFormFactory;
    }

    public function setFiltrationFormFactory(?FormFactoryInterface $filtrationFormFactory): static
    {
        $this->ensureNotLocked();

        $this->filtrationFormFactory = $filtrationFormFactory;

        return $this;
    }

    public function getDefaultFiltrationData(): ?FiltrationData
    {
        return $this->defaultFiltrationData;
    }

    public function setDefaultFiltrationData(?FiltrationData $defaultFiltrationData): static
    {
        $this->ensureNotLocked();

        $this->defaultFiltrationData = $defaultFiltrationData;

        return $this;
    }

    public function isSortingEnabled(): bool
    {
        return $this->sortingEnabled;
    }

    public function setSortingEnabled(bool $sortingEnabled): static
    {
        $this->ensureNotLocked();

        $this->sortingEnabled = $sortingEnabled;

        return $this;
    }

    public function isSortingPersistenceEnabled(): bool
    {
        return $this->sortingPersistenceEnabled;
    }

    public function setSortingPersistenceEnabled(bool $sortingPersistenceEnabled): static
    {
        $this->ensureNotLocked();

        $this->sortingPersistenceEnabled = $sortingPersistenceEnabled;

        return $this;
    }

    public function isSortingClearable(): bool
    {
        return $this->sortingClearable;
    }

    public function setSortingClearable(bool $sortingClearable): static
    {
        $this->ensureNotLocked();

        $this->sortingClearable = $sortingClearable;

        return $this;
    }

    public function getSortingPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->sortingPersistenceAdapter;
    }

    public function setSortingPersistenceAdapter(?PersistenceAdapterInterface $sortingPersistenceAdapter): static
    {
        $this->ensureNotLocked();

        $this->sortingPersistenceAdapter = $sortingPersistenceAdapter;

        return $this;
    }

    public function getSortingPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface
    {
        return $this->sortingPersistenceSubjectProvider;
    }

    public function setSortingPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $sortingPersistenceSubjectProvider): static
    {
        $this->ensureNotLocked();

        $this->sortingPersistenceSubjectProvider = $sortingPersistenceSubjectProvider;

        return $this;
    }

    public function getDefaultSortingData(): ?SortingData
    {
        return $this->defaultSortingData;
    }

    public function setDefaultSortingData(?SortingData $defaultSortingData): static
    {
        $this->ensureNotLocked();

        $this->defaultSortingData = $defaultSortingData;

        return $this;
    }

    public function isPaginationEnabled(): bool
    {
        return $this->paginationEnabled;
    }

    public function setPaginationEnabled(bool $paginationEnabled): static
    {
        $this->ensureNotLocked();

        $this->paginationEnabled = $paginationEnabled;

        return $this;
    }

    public function isPaginationPersistenceEnabled(): bool
    {
        return $this->paginationPersistenceEnabled;
    }

    public function setPaginationPersistenceEnabled(bool $paginationPersistenceEnabled): static
    {
        $this->ensureNotLocked();

        $this->paginationPersistenceEnabled = $paginationPersistenceEnabled;

        return $this;
    }

    public function getPaginationPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->paginationPersistenceAdapter;
    }

    public function setPaginationPersistenceAdapter(?PersistenceAdapterInterface $paginationPersistenceAdapter): static
    {
        $this->ensureNotLocked();

        $this->paginationPersistenceAdapter = $paginationPersistenceAdapter;

        return $this;
    }

    public function getPaginationPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface
    {
        return $this->paginationPersistenceSubjectProvider;
    }

    public function setPaginationPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $paginationPersistenceSubjectProvider): static
    {
        $this->ensureNotLocked();

        $this->paginationPersistenceSubjectProvider = $paginationPersistenceSubjectProvider;

        return $this;
    }

    public function getDefaultPaginationData(): ?PaginationData
    {
        return $this->defaultPaginationData;
    }

    public function setDefaultPaginationData(?PaginationData $defaultPaginationData): static
    {
        $this->ensureNotLocked();

        $this->defaultPaginationData = $defaultPaginationData;

        return $this;
    }

    public function getRequestHandler(): ?RequestHandlerInterface
    {
        return $this->requestHandler;
    }

    public function setRequestHandler(?RequestHandlerInterface $requestHandler): static
    {
        $this->ensureNotLocked();

        $this->requestHandler = $requestHandler;

        return $this;
    }

    public function getPaginationFactory(): ?PaginationFactoryInterface
    {
        return $this->paginationFactory;
    }

    public function setPaginationFactory(?PaginationFactoryInterface $paginationFactory): static
    {
        $this->ensureNotLocked();

        $this->paginationFactory = $paginationFactory;

        return $this;
    }

    public function getGroupBy(): string|callable|null
    {
        return $this->groupBy;
    }

    public function setGroupBy(string|callable|null $groupBy): static
    {
        $this->ensureNotLocked();

        $this->groupBy = $groupBy;

        return $this;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }

    public function addTheme(string $theme): static
    {
        $this->ensureNotLocked();

        $this->themes[] = $theme;

        return $this;
    }

    public function setThemes(array $themes): static
    {
        $this->ensureNotLocked();

        $this->themes = $themes;

        return $this;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    public function setAttribute(string $name, mixed $value): static
    {
        $this->ensureNotLocked();

        $this->attributes[$name] = $value;

        return $this;
    }

    public function setAttributes(array $attributes): static
    {
        $this->ensureNotLocked();

        $this->attributes = $attributes;

        return $this;
    }

    public function getHeaderRowAttributes(): array
    {
        return $this->headerRowAttributes;
    }

    public function hasHeaderRowAttribute(string $name): bool
    {
        return array_key_exists($name, $this->headerRowAttributes);
    }

    public function getHeaderRowAttribute(string $name, mixed $default = null): mixed
    {
        return array_key_exists($name, $this->headerRowAttributes) ? $this->headerRowAttributes[$name] : $default;
    }

    public function setHeaderRowAttribute(string $name, mixed $value): static
    {
        $this->ensureNotLocked();

        $this->headerRowAttributes[$name] = $value;

        return $this;
    }

    public function setHeaderRowAttributes(array $headerRowAttributes): static
    {
        $this->ensureNotLocked();

        $this->headerRowAttributes = $headerRowAttributes;

        return $this;
    }

    public function getValueRowAttributes(): array
    {
        return $this->valueRowAttributes;
    }

    public function hasValueRowAttribute(string $name): bool
    {
        return array_key_exists($name, $this->valueRowAttributes);
    }

    public function getValueRowAttribute(string $name, mixed $default = null): mixed
    {
        return array_key_exists($name, $this->valueRowAttributes) ? $this->valueRowAttributes[$name] : $default;
    }

    public function setValueRowAttribute(string $name, mixed $value): static
    {
        $this->ensureNotLocked();

        $this->valueRowAttributes[$name] = $value;

        return $this;
    }

    public function setValueRowAttributes(array $valueRowAttributes): static
    {
        $this->ensureNotLocked();

        $this->valueRowAttributes = $valueRowAttributes;

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

    public function getColumnVisibilityGroupParameterName(): string
    {
        return $this->getParameterName(static::COLUMN_VISIBILITY_GROUP_PARAMETER);
    }

    public function getDataTableConfig(): DataTableConfigInterface
    {
        $this->ensureNotLocked();

        $config = clone $this;
        $config->locked = true;

        return $config;
    }

    private function getParameterName(string $prefix): string
    {
        return implode('_', array_filter([$prefix, $this->name]));
    }
}

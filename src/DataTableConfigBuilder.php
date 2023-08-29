<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class DataTableConfigBuilder implements DataTableConfigBuilderInterface
{
    private bool $paginationEnabled = true;
    private bool $sortingEnabled = true;
    private bool $filtrationEnabled = true;
    private bool $personalizationEnabled = true;
    private bool $exportingEnabled = true;

    private bool $paginationPersistenceEnabled = true;
    private bool $sortingPersistenceEnabled = true;
    private bool $filtrationPersistenceEnabled = true;
    private bool $personalizationPersistenceEnabled = true;

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

    private array $themes = [];
    private array $headerRowAttributes = [];
    private array $valueRowAttributes = [];

    protected bool $locked = false;

    public function __construct(
        private string $name,
        private ResolvedDataTableTypeInterface $type,
        private EventDispatcherInterface $dispatcher,
        private array $options = [],
    ) {
    }

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

    public function getType(): ResolvedDataTableTypeInterface
    {
        return $this->type;
    }

    public function setType(ResolvedDataTableTypeInterface $type): static
    {
        $this->type = $type;

        return $this;
    }

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
        return $this->options[$name] ?? $default;
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

    public function getActionFactory(): ActionFactoryInterface
    {
        return $this->actionFactory;
    }

    public function setActionFactory(ActionFactoryInterface $actionFactory): static
    {
        $this->actionFactory = $actionFactory;

        return $this;
    }

    public function getExporterFactory(): ExporterFactoryInterface
    {
        return $this->exporterFactory;
    }

    public function setExporterFactory(ExporterFactoryInterface $exporterFactory): static
    {
        $this->exporterFactory = $exporterFactory;

        return $this;
    }

    public function isExportingEnabled(): bool
    {
        return $this->exportingEnabled;
    }

    public function setExportingEnabled(bool $exportingEnabled): static
    {
        $this->exportingEnabled = $exportingEnabled;

        return $this;
    }

    public function getExportFormFactory(): ?FormFactoryInterface
    {
        return $this->exportFormFactory;
    }

    public function setExportFormFactory(?FormFactoryInterface $exportFormFactory): static
    {
        $this->exportFormFactory = $exportFormFactory;

        return $this;
    }

    public function getDefaultExportData(): ?ExportData
    {
        return $this->defaultExportData;
    }

    public function setDefaultExportData(?ExportData $defaultExportData): static
    {
        $this->defaultExportData = $defaultExportData;

        return $this;
    }

    public function isPersonalizationEnabled(): bool
    {
        return $this->personalizationEnabled;
    }

    public function setPersonalizationEnabled(bool $personalizationEnabled): static
    {
        $this->personalizationEnabled = $personalizationEnabled;

        return $this;
    }

    public function isPersonalizationPersistenceEnabled(): bool
    {
        return $this->personalizationPersistenceEnabled;
    }

    public function setPersonalizationPersistenceEnabled(bool $personalizationPersistenceEnabled): static
    {
        $this->personalizationPersistenceEnabled = $personalizationPersistenceEnabled;

        return $this;
    }

    public function getPersonalizationPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->personalizationPersistenceAdapter;
    }

    public function setPersonalizationPersistenceAdapter(?PersistenceAdapterInterface $personalizationPersistenceAdapter): static
    {
        $this->personalizationPersistenceAdapter = $personalizationPersistenceAdapter;

        return $this;
    }

    public function getPersonalizationPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface
    {
        return $this->personalizationPersistenceSubjectProvider;
    }

    public function setPersonalizationPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $personalizationPersistenceSubjectProvider): static
    {
        $this->personalizationPersistenceSubjectProvider = $personalizationPersistenceSubjectProvider;

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

    public function getDefaultPersonalizationData(): ?PersonalizationData
    {
        return $this->defaultPersonalizationData;
    }

    public function setDefaultPersonalizationData(?PersonalizationData $defaultPersonalizationData): static
    {
        $this->defaultPersonalizationData = $defaultPersonalizationData;

        return $this;
    }

    public function isFiltrationEnabled(): bool
    {
        return $this->filtrationEnabled;
    }

    public function setFiltrationEnabled(bool $filtrationEnabled): static
    {
        $this->filtrationEnabled = $filtrationEnabled;

        return $this;
    }

    public function isFiltrationPersistenceEnabled(): bool
    {
        return $this->filtrationPersistenceEnabled;
    }

    public function setFiltrationPersistenceEnabled(bool $filtrationPersistenceEnabled): static
    {
        $this->filtrationPersistenceEnabled = $filtrationPersistenceEnabled;

        return $this;
    }

    public function getFiltrationPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->filtrationPersistenceAdapter;
    }

    public function setFiltrationPersistenceAdapter(?PersistenceAdapterInterface $filtrationPersistenceAdapter): static
    {
        $this->filtrationPersistenceAdapter = $filtrationPersistenceAdapter;

        return $this;
    }

    public function getFiltrationPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface
    {
        return $this->filtrationPersistenceSubjectProvider;
    }

    public function setFiltrationPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $filtrationPersistenceSubjectProvider): static
    {
        $this->filtrationPersistenceSubjectProvider = $filtrationPersistenceSubjectProvider;

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

    public function getDefaultFiltrationData(): ?FiltrationData
    {
        return $this->defaultFiltrationData;
    }

    public function setDefaultFiltrationData(?FiltrationData $defaultFiltrationData): static
    {
        $this->defaultFiltrationData = $defaultFiltrationData;

        return $this;
    }

    public function isSortingEnabled(): bool
    {
        return $this->sortingEnabled;
    }

    public function setSortingEnabled(bool $sortingEnabled): static
    {
        $this->sortingEnabled = $sortingEnabled;

        return $this;
    }

    public function isSortingPersistenceEnabled(): bool
    {
        return $this->sortingPersistenceEnabled;
    }

    public function setSortingPersistenceEnabled(bool $sortingPersistenceEnabled): static
    {
        $this->sortingPersistenceEnabled = $sortingPersistenceEnabled;

        return $this;
    }

    public function getSortingPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->sortingPersistenceAdapter;
    }

    public function setSortingPersistenceAdapter(?PersistenceAdapterInterface $sortingPersistenceAdapter): static
    {
        $this->sortingPersistenceAdapter = $sortingPersistenceAdapter;

        return $this;
    }

    public function getSortingPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface
    {
        return $this->sortingPersistenceSubjectProvider;
    }

    public function setSortingPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $sortingPersistenceSubjectProvider): static
    {
        $this->sortingPersistenceSubjectProvider = $sortingPersistenceSubjectProvider;

        return $this;
    }

    public function getDefaultSortingData(): ?SortingData
    {
        return $this->defaultSortingData;
    }

    public function setDefaultSortingData(?SortingData $defaultSortingData): static
    {
        $this->defaultSortingData = $defaultSortingData;

        return $this;
    }

    public function isPaginationEnabled(): bool
    {
        return $this->paginationEnabled;
    }

    public function setPaginationEnabled(bool $paginationEnabled): static
    {
        $this->paginationEnabled = $paginationEnabled;

        return $this;
    }

    public function isPaginationPersistenceEnabled(): bool
    {
        return $this->paginationPersistenceEnabled;
    }

    public function setPaginationPersistenceEnabled(bool $paginationPersistenceEnabled): static
    {
        $this->paginationPersistenceEnabled = $paginationPersistenceEnabled;

        return $this;
    }

    public function getPaginationPersistenceAdapter(): ?PersistenceAdapterInterface
    {
        return $this->paginationPersistenceAdapter;
    }

    public function setPaginationPersistenceAdapter(?PersistenceAdapterInterface $paginationPersistenceAdapter): static
    {
        $this->paginationPersistenceAdapter = $paginationPersistenceAdapter;

        return $this;
    }

    public function getPaginationPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface
    {
        return $this->paginationPersistenceSubjectProvider;
    }

    public function setPaginationPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $paginationPersistenceSubjectProvider): static
    {
        $this->paginationPersistenceSubjectProvider = $paginationPersistenceSubjectProvider;

        return $this;
    }

    public function getDefaultPaginationData(): ?PaginationData
    {
        return $this->defaultPaginationData;
    }

    public function setDefaultPaginationData(?PaginationData $defaultPaginationData): static
    {
        $this->defaultPaginationData = $defaultPaginationData;

        return $this;
    }

    public function getRequestHandler(): ?RequestHandlerInterface
    {
        return $this->requestHandler;
    }

    public function setRequestHandler(?RequestHandlerInterface $requestHandler): static
    {
        $this->requestHandler = $requestHandler;

        return $this;
    }

    public function getThemes(): array
    {
        return $this->themes;
    }

    public function setThemes(array $themes): static
    {
        $this->themes = $themes;

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
        return $this->headerRowAttributes[$name] ?? $default;
    }

    public function setHeaderRowAttribute(string $name, mixed $value): static
    {
        $this->headerRowAttributes[$name] = $value;

        return $this;
    }

    public function setHeaderRowAttributes(array $headerRowAttributes): static
    {
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
        return $this->valueRowAttributes[$name] ?? $default;
    }

    public function setValueRowAttribute(string $name, mixed $value): static
    {
        $this->valueRowAttributes[$name] = $value;

        return $this;
    }

    public function setValueRowAttributes(array $valueRowAttributes): static
    {
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

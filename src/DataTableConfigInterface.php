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
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

interface DataTableConfigInterface
{
    public const PAGE_PARAMETER = 'page';
    public const PER_PAGE_PARAMETER = 'limit';
    public const SORT_PARAMETER = 'sort';
    public const FILTRATION_PARAMETER = 'filter';
    public const PERSONALIZATION_PARAMETER = 'personalization';
    public const EXPORT_PARAMETER = 'export';

    public function getEventDispatcher(): EventDispatcherInterface;

    public function getName(): string;

    public function getType(): ResolvedDataTableTypeInterface;

    public function getOptions(): array;

    public function hasOption(string $name): bool;

    public function getOption(string $name, mixed $default = null): mixed;

    public function getThemes(): array;

    public function getColumnFactory(): ColumnFactoryInterface;

    public function getFilterFactory(): FilterFactoryInterface;

    public function getActionFactory(): ActionFactoryInterface;

    public function getExporterFactory(): ExporterFactoryInterface;

    public function isExportingEnabled(): bool;

    public function getExportFormFactory(): ?FormFactoryInterface;

    public function getDefaultExportData(): ?ExportData;

    public function isPersonalizationEnabled(): bool;

    public function isPersonalizationPersistenceEnabled(): bool;

    public function getPersonalizationPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getPersonalizationPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface;

    public function getPersonalizationFormFactory(): ?FormFactoryInterface;

    public function getDefaultPersonalizationData(): ?PersonalizationData;

    public function isFiltrationEnabled(): bool;

    public function isFiltrationPersistenceEnabled(): bool;

    public function getFiltrationPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getFiltrationPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface;

    public function getFiltrationFormFactory(): ?FormFactoryInterface;

    public function getDefaultFiltrationData(): ?FiltrationData;

    public function isSortingEnabled(): bool;

    public function isSortingPersistenceEnabled(): bool;

    public function getSortingPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getSortingPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface;

    public function getDefaultSortingData(): ?SortingData;

    public function isPaginationEnabled(): bool;

    public function isPaginationPersistenceEnabled(): bool;

    public function getPaginationPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getPaginationPersistenceSubjectProvider(): ?PersistenceSubjectProviderInterface;

    public function getDefaultPaginationData(): ?PaginationData;

    public function getRequestHandler(): ?RequestHandlerInterface;

    public function getHeaderRowAttributes(): array;

    public function hasHeaderRowAttribute(string $name): bool;

    public function getHeaderRowAttribute(string $name, mixed $default = null): mixed;

    public function getValueRowAttributes(): array;

    public function hasValueRowAttribute(string $name): bool;

    public function getValueRowAttribute(string $name, mixed $default = null): mixed;

    public function getPageParameterName(): string;

    public function getPerPageParameterName(): string;

    public function getSortParameterName(): string;

    public function getFiltrationParameterName(): string;

    public function getPersonalizationParameterName(): string;

    public function getExportParameterName(): string;
}

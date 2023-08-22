<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\TranslatableMessage;

interface DataTableConfigInterface
{
    public const PAGE_PARAMETER = 'page';
    public const PER_PAGE_PARAMETER = 'limit';
    public const SORT_PARAMETER = 'sort';
    public const FILTRATION_PARAMETER = 'filter';
    public const PERSONALIZATION_PARAMETER = 'personalization';
    public const EXPORT_PARAMETER = 'export';

    public const PERSISTENCE_CONTEXTS = [
        'sorting',
        'pagination',
        'filtration',
        'personalization',
    ];

    public function getName(): string;

    public function getType(): ResolvedDataTableTypeInterface;

    public function getOptions(): array;

    public function getThemes(): array;

    public function getTitle(): null|string|TranslatableMessage;

    public function getTitleTranslationParameters(): array;

    public function getTranslationDomain(): null|bool|string;

    /**
     * @return array<FilterInterface>
     */
    public function getFilters(): array;

    /**
     * @throws \InvalidArgumentException if filter of given name does not exist
     */
    public function getFilter(string $name): FilterInterface;

    /**
     * @return array<ExporterInterface>
     */
    public function getExporters(): array;

    /**
     * @throws \InvalidArgumentException if exporter of given name does not exist
     */
    public function getExporter(string $name): ExporterInterface;

    public function isExportingEnabled(): bool;

    public function getExportFormFactory(): ?FormFactoryInterface;

    public function getDefaultExportData(): ?ExportData;

    public function isPersonalizationEnabled(): bool;

    public function isPersonalizationPersistenceEnabled(): bool;

    public function getPersonalizationPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getPersonalizationPersistenceSubject(): ?PersistenceSubjectInterface;

    public function getPersonalizationFormFactory(): ?FormFactoryInterface;

    public function getDefaultPersonalizationData(): ?PersonalizationData;

    public function isFiltrationEnabled(): bool;

    public function isFiltrationPersistenceEnabled(): bool;

    public function getFiltrationPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getFiltrationPersistenceSubject(): ?PersistenceSubjectInterface;

    public function getFiltrationFormFactory(): ?FormFactoryInterface;

    public function getDefaultFiltrationData(): ?FiltrationData;

    public function isSortingEnabled(): bool;

    public function isSortingPersistenceEnabled(): bool;

    public function getSortingPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getSortingPersistenceSubject(): ?PersistenceSubjectInterface;

    public function getDefaultSortingData(): ?SortingData;

    public function isPaginationEnabled(): bool;

    public function isPaginationPersistenceEnabled(): bool;

    public function getPaginationPersistenceAdapter(): ?PersistenceAdapterInterface;

    public function getPaginationPersistenceSubject(): ?PersistenceSubjectInterface;

    public function getDefaultPaginationData(): ?PaginationData;

    public function getRequestHandler(): ?RequestHandlerInterface;

    public function getHeaderRowAttributes(): array;

    public function getValueRowAttributes(): array;

    public function getPageParameterName(): string;

    public function getPerPageParameterName(): string;

    public function getSortParameterName(): string;

    public function getFiltrationParameterName(): string;

    public function getPersonalizationParameterName(): string;

    public function getExportParameterName(): string;
}

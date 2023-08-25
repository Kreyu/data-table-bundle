<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Translation\TranslatableMessage;

interface DataTableConfigBuilderInterface extends DataTableConfigInterface
{
    public function setName(string $name): static;

    public function setType(ResolvedDataTableTypeInterface $type): static;

    public function setOptions(array $options): static;

    public function setThemes(array $themes): static;

    public function addTheme(string $theme): static;

    public function removeTheme(string $theme): static;

    public function setTitle(null|string|TranslatableMessage $title): static;

    public function setTitleTranslationParameters(array $titleTranslationParameters): static;

    public function setTranslationDomain(null|bool|string $translationDomain): static;

    public function getColumnFactory(): ColumnFactoryInterface;

    public function setColumnFactory(ColumnFactoryInterface $columnFactory): static;

    public function getFilterFactory(): FilterFactoryInterface;

    public function setFilterFactory(FilterFactoryInterface $filterFactory): static;

    public function getActionFactory(): ActionFactoryInterface;

    public function setActionFactory(ActionFactoryInterface $actionFactory): static;

    /**
     * @param class-string<ExporterTypeInterface> $type
     */
    public function addExporter(string $name, string $type, array $options = []): static;

    public function removeExporter(string $name): static;

    public function getExporterFactory(): ExporterFactoryInterface;

    public function setExporterFactory(ExporterFactoryInterface $exporterFactory): static;

    public function setExportingEnabled(bool $exportingEnabled): static;

    public function setExportFormFactory(?FormFactoryInterface $exportFormFactory): static;

    public function setDefaultExportData(?ExportData $defaultExportData): static;

    public function setPersonalizationEnabled(bool $personalizationEnabled): static;

    public function setPersonalizationPersistenceEnabled(bool $personalizationPersistenceEnabled): static;

    public function setPersonalizationPersistenceAdapter(?PersistenceAdapterInterface $personalizationPersistenceAdapter): static;

    public function setPersonalizationPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $personalizationPersistenceSubjectProvider): static;

    public function setPersonalizationFormFactory(?FormFactoryInterface $personalizationFormFactory): static;

    public function setDefaultPersonalizationData(?PersonalizationData $defaultPersonalizationData): static;

    public function setFiltrationEnabled(bool $filtrationEnabled): static;

    public function setFiltrationPersistenceEnabled(bool $filtrationPersistenceEnabled): static;

    public function setFiltrationPersistenceAdapter(?PersistenceAdapterInterface $filtrationPersistenceAdapter): static;

    public function setFiltrationPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $filtrationPersistenceSubjectProvider): static;

    public function setFiltrationFormFactory(?FormFactoryInterface $filtrationFormFactory): static;

    public function setDefaultFiltrationData(?FiltrationData $defaultFiltrationData): static;

    public function setSortingEnabled(bool $sortingEnabled): static;

    public function setSortingPersistenceEnabled(bool $sortingPersistenceEnabled): static;

    public function setSortingPersistenceAdapter(?PersistenceAdapterInterface $sortingPersistenceAdapter): static;

    public function setSortingPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $sortingPersistenceSubjectProvider): static;

    public function setDefaultSortingData(?SortingData $defaultSortingData): static;

    public function setPaginationEnabled(bool $paginationEnabled): static;

    public function setPaginationPersistenceEnabled(bool $paginationPersistenceEnabled): static;

    public function setPaginationPersistenceAdapter(?PersistenceAdapterInterface $paginationPersistenceAdapter): static;

    public function setPaginationPersistenceSubjectProvider(?PersistenceSubjectProviderInterface $paginationPersistenceSubjectProvider): static;

    public function setDefaultPaginationData(?PaginationData $defaultPaginationData): static;

    public function setRequestHandler(?RequestHandlerInterface $requestHandler): static;

    public function setHeaderRowAttributes(array $headerRowAttributes): static;

    public function setValueRowAttributes(array $valueRowAttributes): static;

    public function getDataTableConfig(): DataTableConfigInterface;
}

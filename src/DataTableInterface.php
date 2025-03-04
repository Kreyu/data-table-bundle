<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\OutOfBoundsException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\Form\FormBuilderInterface;

interface DataTableInterface
{
    public function initialize(): void;

    public function getName(): string;

    public function getQuery(): ProxyQueryInterface;

    public function getConfig(): DataTableConfigInterface;

    /**
     * @return array<string, ColumnInterface>
     */
    public function getColumns(): array;

    /**
     * @return array<string, ColumnInterface>
     */
    public function getVisibleColumns(): array;

    /**
     * @return array<string, ColumnInterface>
     */
    public function getHiddenColumns(): array;

    /**
     * @return array<string, ColumnInterface>
     */
    public function getExportableColumns(): array;

    /**
     * @throws OutOfBoundsException if column of given name does not exist
     */
    public function getColumn(string $name): ColumnInterface;

    public function hasColumn(string $name): bool;

    /**
     * @param class-string<ColumnTypeInterface> $type
     */
    public function addColumn(ColumnInterface|string $column, string $type = ColumnType::class, array $options = []): static;

    public function removeColumn(string $name): static;

    /**
     * @return array<string, FilterInterface>
     */
    public function getFilters(): array;

    /**
     * @throws OutOfBoundsException if filter of given name does not exist
     */
    public function getFilter(string $name): FilterInterface;

    public function hasFilter(string $name): bool;

    /**
     * @param class-string<FilterTypeInterface> $type
     */
    public function addFilter(FilterInterface|string $filter, string $type = FilterType::class, array $options = []): static;

    public function removeFilter(string $name): static;

    /**
     * @return array<string, ActionInterface>
     */
    public function getActions(): array;

    /**
     * @throws OutOfBoundsException if action of given name does not exist
     */
    public function getAction(string $name): ActionInterface;

    public function hasAction(string $name): bool;

    /**
     * @param class-string<ActionTypeInterface> $type
     */
    public function addAction(ActionInterface|string $action, string $type = ActionType::class, array $options = []): static;

    public function removeAction(string $name): static;

    /**
     * @return array<string, ActionInterface>
     */
    public function getBatchActions(): array;

    /**
     * @throws OutOfBoundsException if batch action of given name does not exist
     */
    public function getBatchAction(string $name): ActionInterface;

    public function hasBatchAction(string $name): bool;

    /**
     * @param class-string<ActionTypeInterface> $type
     */
    public function addBatchAction(ActionInterface|string $action, string $type = ActionType::class, array $options = []): static;

    public function removeBatchAction(string $name): static;

    /**
     * @return array<string, ActionInterface>
     */
    public function getRowActions(): array;

    /**
     * @throws OutOfBoundsException if row action of given name does not exist
     */
    public function getRowAction(string $name): ActionInterface;

    public function hasRowAction(string $name): bool;

    /**
     * @param class-string<ActionTypeInterface> $type
     */
    public function addRowAction(ActionInterface|string $action, string $type = ActionType::class, array $options = []): static;

    public function removeRowAction(string $name): static;

    /**
     * @return array<string, ExporterInterface>
     */
    public function getExporters(): array;

    /**
     * @throws OutOfBoundsException if exporter of given name does not exist
     */
    public function getExporter(string $name): ExporterInterface;

    public function hasExporter(string $name): bool;

    /**
     * @param class-string<ExporterTypeInterface> $type
     */
    public function addExporter(ExporterInterface|string $exporter, string $type = ExporterType::class, array $options = []): static;

    public function removeExporter(string $name): static;

    public function sort(SortingData $data): void;

    public function filter(FiltrationData $data): void;

    public function paginate(PaginationData $data): void;

    public function personalize(PersonalizationData $data): void;

    public function export(?ExportData $data = null): ExportFile;

    public function getItems(): iterable;

    public function getPagination(): PaginationInterface;

    public function getSortingData(): ?SortingData;

    public function setSortingData(?SortingData $sortingData): static;

    public function setPaginationData(?PaginationData $paginationData): static;

    public function getPaginationData(): ?PaginationData;

    public function setFiltrationData(?FiltrationData $filtrationData): static;

    public function getFiltrationData(): ?FiltrationData;

    public function setPersonalizationData(?PersonalizationData $personalizationData): static;

    public function getPersonalizationData(): ?PersonalizationData;

    public function setExportData(?ExportData $exportData): static;

    public function getExportData(): ?ExportData;

    public function getTurboFrameIdentifier(): string;

    public function createFiltrationFormBuilder(?DataTableView $view = null): FormBuilderInterface;

    public function createPersonalizationFormBuilder(?DataTableView $view = null): FormBuilderInterface;

    public function createExportFormBuilder(?DataTableView $view = null): FormBuilderInterface;

    public function isExporting(): bool;

    public function hasActiveFilters(): bool;

    public function handleRequest(mixed $request): void;

    public function createView(): DataTableView;

    public function createExportView(): DataTableView;
}

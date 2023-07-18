<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Exception\OutOfBoundsException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\Form\FormBuilderInterface;

interface DataTableInterface
{
    public function initialize(): void;

    public function getQuery(): ProxyQueryInterface;

    public function getConfig(): DataTableConfigInterface;

    /**
     * @return array<string, ActionInterface>
     */
    public function getActions(): array;

    /**
     * @throws OutOfBoundsException if action of given name does not exist
     */
    public function getAction(string $name): ActionInterface;

    public function hasAction(string $name): bool;

    public function addAction(ActionInterface|string $action, string $type = null, array $options = []): static;

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

    public function addBatchAction(ActionInterface|string $action, string $type = null, array $options = []): static;

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

    public function addRowAction(ActionInterface|string $action, string $type = null, array $options = []): static;

    public function removeRowAction(string $name): static;

    public function sort(SortingData $data): void;

    public function filter(FiltrationData $data): void;

    public function paginate(PaginationData $data): void;

    public function personalize(PersonalizationData $data): void;

    public function export(ExportData $data = null): ExportFile;

    public function getPagination(): PaginationInterface;

    public function getSortingData(): SortingData;

    public function getPaginationData(): PaginationData;

    public function getFiltrationData(): ?FiltrationData;

    public function getPersonalizationData(): PersonalizationData;

    public function getExportData(): ?ExportData;

    public function createFiltrationFormBuilder(DataTableView $view = null): FormBuilderInterface;

    public function createPersonalizationFormBuilder(DataTableView $view = null): FormBuilderInterface;

    public function createExportFormBuilder(): FormBuilderInterface;

    public function isExporting(): bool;

    public function hasActiveFilters(): bool;

    public function handleRequest(mixed $request): void;

    public function createView(): DataTableView;
}

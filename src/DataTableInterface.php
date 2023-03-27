<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportFile;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Symfony\Component\Form\FormInterface;

interface DataTableInterface
{
    public function getConfig(): DataTableConfigInterface;

    public function sort(SortingData $data): void;

    public function filter(FiltrationData $data): void;

    public function paginate(PaginationData $data): void;

    public function personalize(PersonalizationData $data): void;

    public function export(ExportData $exportData = null): ExportFile;

    public function isExporting(): bool;

    public function hasActiveFilters(): bool;

    public function handleRequest(mixed $request): void;

    public function getPagination(): PaginationInterface;

    public function getSortingData(): ?SortingData;

    public function getPaginationData(): ?PaginationData;

    public function getFiltrationData(): ?FiltrationData;

    public function getPersonalizationData(): ?PersonalizationData;

    public function getExportData(): ?ExportData;

    public function createView(): DataTableView;
}

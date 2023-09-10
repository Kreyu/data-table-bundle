<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;

interface ExporterInterface
{
    public function getName(): string;

    public function getConfig(): ExporterConfigInterface;

    public function getDataTable(): DataTableInterface;

    public function setDataTable(DataTableInterface $dataTable): static;

    public function export(DataTableView $view, string $filename = 'export'): ExportFile;
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

interface DataTableDataExtractorInterface
{
    public function extractDataTableConfiguration(DataTableInterface $dataTable): array;

    public function extractColumnConfiguration(ColumnInterface $column): array;

    public function extractFilterConfiguration(FilterInterface $filter): array;

    public function extractActionConfiguration(ActionInterface $action): array;

    public function extractExporterConfiguration(ExporterInterface $exporter): array;

    public function extractValueRows(DataTableView $view): array;
}

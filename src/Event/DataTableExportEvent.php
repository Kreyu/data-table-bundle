<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;

class DataTableExportEvent extends DataTableEvent
{
    public function __construct(DataTableInterface $dataTable, private ExportData $exportData)
    {
        parent::__construct($dataTable);
    }

    public function getExportData(): ExportData
    {
        return $this->exportData;
    }

    public function setExportData(ExportData $exportData): void
    {
        $this->exportData = $exportData;
    }
}
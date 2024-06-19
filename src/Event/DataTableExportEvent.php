<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;

class DataTableExportEvent extends DataTableEvent
{
    public function __construct(
        DataTableInterface $dataTable,
        private ExportData $data,
    ) {
        parent::__construct($dataTable);
    }

    /**
     * @deprecated use {@see getData()} instead
     */
    public function getExportData(): ExportData
    {
        return $this->data;
    }

    /**
     * @deprecated use {@see getData()} instead
     */
    public function setExportData(ExportData $exportData): void
    {
        $this->data = $exportData;
    }

    public function getData(): ExportData
    {
        return $this->data;
    }

    public function setData(ExportData $data): void
    {
        $this->data = $data;
    }
}

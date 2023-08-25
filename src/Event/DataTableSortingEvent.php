<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExportData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;

class DataTableSortingEvent extends DataTableEvent
{
    public function __construct(DataTableInterface $dataTable, private SortingData $sortingData)
    {
        parent::__construct($dataTable);
    }

    public function getSortingData(): SortingData
    {
        return $this->sortingData;
    }

    public function setSortingData(SortingData $sortingData): void
    {
        $this->sortingData = $sortingData;
    }
}

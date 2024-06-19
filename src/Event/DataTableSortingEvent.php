<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;

class DataTableSortingEvent extends DataTableEvent
{
    public function __construct(
        DataTableInterface $dataTable,
        private SortingData $data,
    ) {
        parent::__construct($dataTable);
    }

    /**
     * @deprecated use {@see getData()} instead
     */
    public function getSortingData(): SortingData
    {
        return $this->data;
    }

    /**
     * @deprecated use {@see setData()} instead
     */
    public function setSortingData(SortingData $sortingData): void
    {
        $this->data = $sortingData;
    }

    public function getData(): SortingData
    {
        return $this->data;
    }

    public function setData(SortingData $data): void
    {
        $this->data = $data;
    }
}

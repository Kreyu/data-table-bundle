<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;

class DataTablePaginationEvent extends DataTableEvent
{
    public function __construct(
        DataTableInterface     $dataTable,
        private PaginationData $data,
    ) {
        parent::__construct($dataTable);
    }

    /**
     * @deprecated use {@see getData()} instead
     */
    public function getPaginationData(): PaginationData
    {
        return $this->data;
    }

    /**
     * @deprecated use {@see getData()} instead
     */
    public function setPaginationData(PaginationData $paginationData): void
    {
        $this->data = $paginationData;
    }

    public function getData(): PaginationData
    {
        return $this->data;
    }

    public function setData(PaginationData $data): void
    {
        $this->data = $data;
    }
}

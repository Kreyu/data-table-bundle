<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;

class DataTablePaginationEvent extends DataTableEvent
{
    public function __construct(DataTableInterface $dataTable, private PaginationData $paginationData)
    {
        parent::__construct($dataTable);
    }

    public function getPaginationData(): PaginationData
    {
        return $this->paginationData;
    }

    public function setPaginationData(PaginationData $paginationData): void
    {
        $this->paginationData = $paginationData;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;

class DataTableFiltrationEvent extends DataTableEvent
{
    public function __construct(
        DataTableInterface $dataTable,
        private FiltrationData $data,
    ) {
        parent::__construct($dataTable);
    }

    /**
     * @deprecated use {@see getData()} instead
     */
    public function getFiltrationData(): FiltrationData
    {
        return $this->data;
    }

    /**
     * @deprecated use {@see getData()} instead
     */
    public function setFiltrationData(FiltrationData $filtrationData): void
    {
        $this->data = $filtrationData;
    }

    public function getData(): FiltrationData
    {
        return $this->data;
    }

    public function setData(FiltrationData $data): void
    {
        $this->data = $data;
    }
}

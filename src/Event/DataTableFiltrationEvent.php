<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;

class DataTableFiltrationEvent extends DataTableEvent
{
    public function __construct(
        DataTableInterface $dataTable,
        private FiltrationData $filtrationData,
    ) {
        parent::__construct($dataTable);
    }

    public function getFiltrationData(): FiltrationData
    {
        return $this->filtrationData;
    }

    public function setFiltrationData(FiltrationData $filtrationData): void
    {
        $this->filtrationData = $filtrationData;
    }
}

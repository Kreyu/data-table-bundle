<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Event;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Symfony\Contracts\EventDispatcher\Event;

class DataTableEvent extends Event
{
    public function __construct(
        private readonly DataTableInterface $dataTable,
    ) {
    }

    public function getDataTable(): DataTableInterface
    {
        return $this->dataTable;
    }
}
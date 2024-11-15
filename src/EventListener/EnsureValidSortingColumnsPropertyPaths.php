<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\EventListener;

use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTableSortingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EnsureValidSortingColumnsPropertyPaths implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DataTableEvents::PRE_SORT => 'ensureValidSortingColumnsPropertyPaths',
        ];
    }

    public function ensureValidSortingColumnsPropertyPaths(DataTableSortingEvent $event): void
    {
        $dataTable = $event->getDataTable();
        $data = $event->getSortingData();

        foreach ($data->getColumns() as $column) {
            if ($dataTable->hasColumn($column->getName())) {
                $column->setPropertyPath($dataTable->getColumn($column->getName())->getSortPropertyPath());
            }
        }
    }
}

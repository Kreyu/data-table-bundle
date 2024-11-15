<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\EventListener;

use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTableFiltrationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RemoveRedundantFilters implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DataTableEvents::PRE_FILTER => 'removeRedundantFilters',
        ];
    }

    public function removeRedundantFilters(DataTableFiltrationEvent $event): void
    {
        $dataTable = $event->getDataTable();
        $data = $event->getFiltrationData();

        foreach ($data->getFilters() as $name => $filter) {
            if (!$dataTable->hasFilter($name)) {
                $data->removeFilter($name);
            }
        }
    }
}

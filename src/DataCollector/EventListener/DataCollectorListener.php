<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\EventListener;

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTableFiltrationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DataCollectorListener implements EventSubscriberInterface
{
    public function __construct(
        readonly private DataTableDataCollectorInterface $dataCollector,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DataTableEvents::POST_FILTER => ['onPostFilter', 255],
        ];
    }

    public function onPostFilter(DataTableFiltrationEvent $event): void
    {
        $this->dataCollector->collectFilter($event->getDataTable(), $event->getFiltrationData());
    }
}

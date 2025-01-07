<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\EventListener;

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTableFiltrationEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePaginationEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTableSortingEvent;
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
            DataTableEvents::POST_INITIALIZE => ['collectDataTable', 255],
            DataTableEvents::POST_FILTER => ['collectFiltrationData', 255],
            DataTableEvents::POST_PAGINATE => ['collectPaginationData', 255],
            DataTableEvents::POST_SORT => ['collectSortingData', 255],
        ];
    }

    public function collectDataTable(DataTableEvent $event): void
    {
        $this->dataCollector->collectDataTable($event->getDataTable());
    }

    public function collectPaginationData(DataTablePaginationEvent $event): void
    {
        $this->dataCollector->collectPaginationData($event->getDataTable(), $event->getPaginationData());
    }

    public function collectFiltrationData(DataTableFiltrationEvent $event): void
    {
        $this->dataCollector->collectFiltrationData($event->getDataTable(), $event->getFiltrationData());
    }

    public function collectSortingData(DataTableSortingEvent $event): void
    {
        $this->dataCollector->collectSortingData($event->getDataTable(), $event->getSortingData());
    }
}

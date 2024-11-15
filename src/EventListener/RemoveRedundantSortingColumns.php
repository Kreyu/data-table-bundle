<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\EventListener;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTableSortingEvent;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingColumnData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RemoveRedundantSortingColumns implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DataTableEvents::PRE_SORT => 'removeRedundantSortingColumns',
        ];
    }

    public function removeRedundantSortingColumns(DataTableSortingEvent $event): void
    {
        $dataTable = $event->getDataTable();
        $data = $event->getSortingData();

        foreach ($data->getColumns() as $column) {
            if ($this->isColumnRedundant($dataTable, $column)) {
                $data->removeColumn($column);
            }
        }
    }

    private function isColumnRedundant(DataTableInterface $dataTable, SortingColumnData $column): bool
    {
        return !$dataTable->hasColumn($column->getName())
            || !$dataTable->getColumn($column->getName())->getConfig()->isSortable();
    }
}

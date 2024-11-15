<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\EventListener;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableEvents;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePersonalizationEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTableSortingEvent;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumnData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingColumnData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RemoveRedundantPersonalizationColumns implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            DataTableEvents::PRE_PERSONALIZE => 'removeRedundantPersonalizationColumns',
        ];
    }

    public function removeRedundantPersonalizationColumns(DataTablePersonalizationEvent $event): void
    {
        $dataTable = $event->getDataTable();
        $data = $event->getPersonalizationData();

        foreach ($data->getColumns() as $column) {
            if ($this->isColumnRedundant($dataTable, $column)) {
                $data->removeColumn($column);
            }
        }
    }

    private function isColumnRedundant(DataTableInterface $dataTable, PersonalizationColumnData $column): bool
    {
        return !$dataTable->hasColumn($column->getName())
            || !$dataTable->getColumn($column->getName())->getConfig()->isPersonalizable();
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Sorting\EventListener;

use Kreyu\Bundle\DataTableBundle\Event\DataTableSortingEvent;

class EnsureValidPropertyPaths
{
    public function __invoke(DataTableSortingEvent $event): void
    {
        $dataTable = $event->getDataTable();

        $data = $event->getData();

        foreach ($data as $name => $sortingColumnData) {
            if (!$dataTable->hasColumn($name)) {
                continue;
            }

            $column = $dataTable->getColumn($name);

            $data = $data->withColumn(
                column: $name,
                direction: $sortingColumnData->getDirection(),
                propertyPath: $column->getPropertyPath(),
            );
        }

        $event->setData($data);
    }
}

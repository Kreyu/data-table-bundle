<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\EventListener;

use Kreyu\Bundle\DataTableBundle\Column\ColumnConfigInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePersonalizationEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTableSortingEvent;
use Kreyu\Bundle\DataTableBundle\EventListener\RemoveRedundantPersonalizationColumns;
use Kreyu\Bundle\DataTableBundle\EventListener\RemoveRedundantSortingColumns;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use PHPUnit\Framework\TestCase;

class RemoveRedundantSortingColumnsTest extends TestCase
{
    public function testRemoveRedundantSortingColumns()
    {
        $dataTable = $this->createMock(DataTableInterface::class);

        $dataTable->expects($this->exactly(3))
            ->method('hasColumn')
            ->willReturnCallback(fn (string $name) => match ($name) {
                'title', 'description' => true,
                'category' => false,
            })
        ;

        $dataTable->expects($this->exactly(2))
            ->method('getColumn')
            ->willReturnCallback(function (string $name) {
                $column = $this->createMock(ColumnInterface::class);
                $columnConfig = $this->createMock(ColumnConfigInterface::class);

                $column->expects($this->once())
                    ->method('getConfig')
                    ->willReturn($columnConfig)
                ;

                $columnConfig->expects($this->once())
                    ->method('isSortable')
                    ->willReturnCallback(fn () => match ($name) {
                        'title' => true,
                        'description' => false,
                    })
                ;

                return $column;
            })
        ;

        $sortingData = SortingData::fromArray([
            'title' => 'desc',
            'description' => 'desc',
            'category' => 'desc',
        ]);

        $event = new DataTableSortingEvent($dataTable, $sortingData);

        $eventListener = new RemoveRedundantSortingColumns();
        $eventListener->removeRedundantSortingColumns($event);

        $columns = $event->getSortingData()->getColumns();

        $this->assertArrayHasKey('title', $columns);
        $this->assertArrayNotHasKey('description', $columns);
        $this->assertArrayNotHasKey('category', $columns);
    }
}
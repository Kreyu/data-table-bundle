<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\EventListener;

use Kreyu\Bundle\DataTableBundle\Column\ColumnConfigInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePersonalizationEvent;
use Kreyu\Bundle\DataTableBundle\EventListener\RemoveRedundantPersonalizationColumns;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use PHPUnit\Framework\TestCase;

class RemoveRedundantPersonalizationColumnsTest extends TestCase
{
    public function testRemoveRedundantPersonalizationColumns()
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
                    ->method('isPersonalizable')
                    ->willReturnCallback(fn () => match ($name) {
                        'title' => true,
                        'description' => false,
                    })
                ;

                return $column;
            })
        ;

        $personalizationData = PersonalizationData::fromArray([
            'columns' => [
                'title' => ['visible' => true],
                'description' => ['visible' => true],
                'category' => ['visible' => true],
            ],
        ]);

        $event = new DataTablePersonalizationEvent($dataTable, $personalizationData);

        $eventListener = new RemoveRedundantPersonalizationColumns();
        $eventListener->removeRedundantPersonalizationColumns($event);

        $columns = $event->getPersonalizationData()->getColumns();

        $this->assertArrayHasKey('title', $columns);
        $this->assertArrayNotHasKey('description', $columns);
        $this->assertArrayNotHasKey('category', $columns);
    }
}
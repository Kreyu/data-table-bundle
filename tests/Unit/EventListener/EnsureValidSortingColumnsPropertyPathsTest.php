<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\EventListener;

use Kreyu\Bundle\DataTableBundle\Column\ColumnConfigInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTablePersonalizationEvent;
use Kreyu\Bundle\DataTableBundle\Event\DataTableSortingEvent;
use Kreyu\Bundle\DataTableBundle\EventListener\EnsureValidSortingColumnsPropertyPaths;
use Kreyu\Bundle\DataTableBundle\EventListener\RemoveRedundantPersonalizationColumns;
use Kreyu\Bundle\DataTableBundle\EventListener\RemoveRedundantSortingColumns;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingColumnData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyPath;

class EnsureValidSortingColumnsPropertyPathsTest extends TestCase
{
    public function testEnsureValidSortingColumnsPropertyPaths()
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

                $column->expects($this->once())
                    ->method('getSortPropertyPath')
                    ->willReturnCallback(fn () => match ($name) {
                        'title' => new PropertyPath('title'),
                        'description' => new PropertyPath('shortDescription'),
                    })
                ;

                return $column;
            })
        ;

        $sortingData = SortingData::fromArray([
            new SortingColumnData('title', 'desc', 'title'),
            new SortingColumnData('description', 'desc', 'description'),
            new SortingColumnData('category', 'desc', 'category'),
        ]);

        $event = new DataTableSortingEvent($dataTable, $sortingData);

        $eventListener = new EnsureValidSortingColumnsPropertyPaths();
        $eventListener->ensureValidSortingColumnsPropertyPaths($event);

        $sortingData = $event->getSortingData();

        $this->assertEquals('title', (string) $sortingData->getColumn('title')->getPropertyPath());
        $this->assertEquals('shortDescription', (string) $sortingData->getColumn('description')->getPropertyPath());
        $this->assertEquals('category', (string) $sortingData->getColumn('category')->getPropertyPath());
    }
}
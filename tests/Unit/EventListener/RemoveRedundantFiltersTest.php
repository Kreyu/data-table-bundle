<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\EventListener;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Event\DataTableFiltrationEvent;
use Kreyu\Bundle\DataTableBundle\EventListener\RemoveRedundantFilters;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use PHPUnit\Framework\TestCase;

class RemoveRedundantFiltersTest extends TestCase
{
    public function testRemoveRedundantFilters()
    {
        $dataTable = $this->createMock(DataTableInterface::class);

        $dataTable->expects($this->exactly(3))
            ->method('hasFilter')
            ->willReturnCallback(fn (string $name) => match ($name) {
                'title', 'description' => true,
                'category' => false,
            })
        ;

        $filtrationData = FiltrationData::fromArray([
            'title' => 'foo',
            'description' => 'bar',
            'category' => 'baz',
        ]);

        $event = new DataTableFiltrationEvent($dataTable, $filtrationData);

        $eventListener = new RemoveRedundantFilters();
        $eventListener->removeRedundantFilters($event);

        $filters = $event->getFiltrationData()->getFilters();

        $this->assertArrayHasKey('title', $filters);
        $this->assertArrayHasKey('description', $filters);
        $this->assertArrayNotHasKey('category', $filters);
    }
}
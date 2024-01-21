<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\EventListener;

use Kreyu\Bundle\DataTableBundle\Filter\Event\PreHandleEvent;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\EventListener\TransformDateRangeFilterData;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TransformDateRangeFilterDataTest extends TestCase
{
    private TransformDateRangeFilterData $listener;

    protected function setUp(): void
    {
        $this->listener = new TransformDateRangeFilterData();
    }

    public static function providePreHandleCases(): iterable
    {
        yield 'empty value' => [
            'given' => new FilterData(),
            'expected' => new FilterData(),
        ];

        yield 'value from only' => [
            'given' => new FilterData(
                value: ['from' => new \DateTime('2022-01-01 11:22:33')],
                operator: Operator::Between,
            ),
            'expected' => new FilterData(
                value: new \DateTime('2022-01-01 00:00:00'),
                operator: Operator::GreaterThanEquals,
            ),
        ];

        yield 'value to only' => [
            'given' => new FilterData(
                value: ['to' => new \DateTime('2022-01-01 11:22:33')],
                operator: Operator::Between,
            ),
            'expected' => new FilterData(
                value: new \DateTime('2022-01-02 00:00:00'),
                operator: Operator::LessThan,
            ),
        ];

        yield 'value from and to' => [
            'given' => new FilterData(
                value: [
                    'from' => new \DateTime('2022-01-01 11:22:33'),
                    'to' => new \DateTime('2022-01-02 11:22:33'),
                ],
                operator: Operator::Between,
            ),
            'expected' => new FilterData(
                value: [
                    'from' => new \DateTime('2022-01-01 00:00:00'),
                    'to' => new \DateTime('2022-01-03 00:00:00'),
                ],
                operator: Operator::Between,
            ),
        ];
    }

    #[DataProvider('providePreHandleCases')]
    public function testPreHandle(FilterData $given, FilterData $expected): void
    {
        $event = new PreHandleEvent(
            $this->createMock(DoctrineOrmProxyQueryInterface::class),
            $given,
            $this->createMock(FilterInterface::class),
        );

        $this->listener->preHandle($event);

        $this->assertEquals($expected, $event->getData());
    }
}

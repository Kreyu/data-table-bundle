<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\Event\FilterEvent;
use Kreyu\Bundle\DataTableBundle\Filter\Event\FilterEvents;
use Kreyu\Bundle\DataTableBundle\Filter\Event\PostHandleEvent;
use Kreyu\Bundle\DataTableBundle\Filter\Event\PreHandleEvent;
use Kreyu\Bundle\DataTableBundle\Filter\Filter;
use Kreyu\Bundle\DataTableBundle\Filter\FilterConfigInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class FilterTest extends TestCase
{
    private MockObject&FilterConfigInterface $filterConfig;
    private MockObject&EventDispatcherInterface $eventDispatcher;

    public function setUp(): void
    {
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->eventDispatcher->method('hasListeners')->willReturn(true);

        $this->filterConfig = $this->createMock(FilterConfigInterface::class);
        $this->filterConfig->method('getEventDispatcher')->willReturn($this->eventDispatcher);
    }

    public function testFormNameStripsDotsFromFilterName(): void
    {
        $this->filterConfig->method('getName')->willReturn('foo.bar');

        $this->assertEquals('foo__bar', $this->createFilter()->getFormName());
    }

    public function testQueryPathDefaultsToFilterName()
    {
        $this->filterConfig->method('getName')->willReturn('foo');
        $this->filterConfig->method('getOption')->willReturnCallback(function (string $name, string $default) {
            $this->assertEquals('query_path', $name);
            $this->assertEquals('foo', $default);

            return $default;
        });

        $this->createFilter()->getQueryPath();
    }

    public function testHandleDispatchesEvents(): void
    {
        $filter = $this->createFilter();

        $query = $this->createMock(ProxyQueryInterface::class);
        $data = $this->createMock(FilterData::class);

        $this->eventDispatcher
            ->expects($matcher = $this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function (FilterEvent $event, string $eventName) use ($matcher, $filter, $query, $data) {
                // @phpstan-ignore-next-line
                $this->assertInstanceOf(match ($matcher->numberOfInvocations()) {
                    1 => PreHandleEvent::class,
                    2 => PostHandleEvent::class,
                }, $event);

                // @phpstan-ignore-next-line
                $this->assertEquals(match ($matcher->numberOfInvocations()) {
                    1 => FilterEvents::PRE_HANDLE,
                    2 => FilterEvents::POST_HANDLE,
                }, $eventName);

                $this->assertEquals($query, $event->getQuery());
                $this->assertEquals($data, $event->getData());
                $this->assertEquals($filter, $event->getFilter());

                return $event;
            });

        $filter->handle($query, $data);
    }

    public function testCreateViewCallsResolvedFilterTypeMethods()
    {
        $filter = $this->createFilter();

        $data = $this->createMock(FilterData::class);
        $parent = $this->createMock(DataTableView::class);
        $view = $this->createMock(FilterView::class);
        $resolvedFilterType = $this->createMock(ResolvedFilterTypeInterface::class);

        $this->filterConfig->method('getType')->willReturn($resolvedFilterType);
        $this->filterConfig->method('getOptions')->willReturn($options = ['foo' => 'bar']);

        $resolvedFilterType
            ->expects($this->once())
            ->method('createView')
            ->willReturnCallback(function ($passedFilter, $passedData, $passedParent) use ($filter, $data, $parent, $view) {
                $this->assertEquals($filter, $passedFilter);
                $this->assertEquals($data, $passedData);
                $this->assertEquals($parent, $passedParent);

                return $view;
            });

        $resolvedFilterType
            ->expects($this->once())
            ->method('buildView')
            ->willReturnCallback(function ($passedView, $passedFilter, $passedData, $passedOptions) use ($view, $filter, $data, $options) {
                $this->assertEquals($view, $passedView);
                $this->assertEquals($filter, $passedFilter);
                $this->assertEquals($data, $passedData);
                $this->assertEquals($options, $passedOptions);
            });

        $this->assertEquals($view, $filter->createView($data, $parent));
    }

    private function createFilter(): FilterInterface
    {
        return new Filter($this->filterConfig);
    }
}

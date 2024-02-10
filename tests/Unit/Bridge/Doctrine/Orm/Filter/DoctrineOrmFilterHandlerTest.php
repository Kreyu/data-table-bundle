<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter;

use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\DoctrineOrmFilterEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\DoctrineOrmFilterEvents;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreApplyExpressionEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event\PreSetParametersEvent;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\DoctrineOrmFilterHandler;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionFactory\ExpressionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ParameterFactory\ParameterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterConfigInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Query\NotSupportedProxyQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DoctrineOrmFilterHandlerTest extends TestCase
{
    private MockObject&FilterInterface $filter;
    private MockObject&DoctrineOrmProxyQueryInterface $query;
    private MockObject&FilterData $data;
    private MockObject&EventDispatcherInterface $eventDispatcher;

    protected function setUp(): void
    {
        $filter = $this->createFilterMock();
        $filterConfig = $this->createFilterConfigMock();
        $eventDispatcher = $this->createEventDispatcherMock();

        $filter->method('getConfig')->willReturn($filterConfig);
        $filterConfig->method('getEventDispatcher')->willReturn($eventDispatcher);

        $this->filter = $filter;
        $this->eventDispatcher = $eventDispatcher;
        $this->query = $this->createDoctrineOrmProxyQueryMock();
        $this->data = $this->createFilterDataMock();
    }

    public function testItThrowsExceptionWithNotSupportedProxyQueryClass(): void
    {
        $query = new NotSupportedProxyQuery();

        $this->expectExceptionObject(new UnexpectedTypeException($query, DoctrineOrmProxyQueryInterface::class));

        $this->createHandler()->handle($query, $this->data, $this->filter);
    }

    public function testItAppliesExpression(): void
    {
        ($queryBuilder = $this->createQueryBuilderMock())
            ->expects($this->once())
            ->method('andWhere')
            ->willReturnCallback(function (mixed $expression) {
                $this->assertEquals('expression', $expression);
            });

        $this->query->method('getQueryBuilder')->willReturn($queryBuilder);

        $this->createHandler(expression: 'expression')->handle($this->query, $this->data, $this->filter);
    }

    public function testItSetsParameter(): void
    {
        ($queryBuilder = $this->createQueryBuilderMock())
            ->expects($this->once())
            ->method('setParameter')
            ->willReturnCallback(function ($name, $value) {
                $this->assertEquals('foo', $name);
                $this->assertEquals('bar', $value);
            });

        $this->query->method('getQueryBuilder')->willReturn($queryBuilder);

        $this->createHandler(parameters: [new Parameter('foo', 'bar')])->handle($this->query, $this->data, $this->filter);
    }

    public function testItDispatchesEvents(): void
    {
        $this->eventDispatcher
            ->expects($matcher = $this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function (DoctrineOrmFilterEvent $event, string $eventName) use ($matcher) {
                // @phpstan-ignore-next-line
                $this->assertInstanceOf(match ($matcher->numberOfInvocations()) {
                    1 => PreSetParametersEvent::class,
                    2 => PreApplyExpressionEvent::class,
                }, $event);

                // @phpstan-ignore-next-line
                $this->assertEquals(match ($matcher->numberOfInvocations()) {
                    1 => DoctrineOrmFilterEvents::PRE_SET_PARAMETERS,
                    2 => DoctrineOrmFilterEvents::PRE_APPLY_EXPRESSION,
                }, $eventName);

                $this->assertEquals($this->query, $event->getQuery());
                $this->assertEquals($this->data, $event->getData());
                $this->assertEquals($this->filter, $event->getFilter());

                return $event;
            });

        $this->createHandler()->handle($this->query, $this->data, $this->filter);
    }

    private function createHandler(mixed $expression = null, array $parameters = []): DoctrineOrmFilterHandler
    {
        $expressionFactory = $this->createMock(ExpressionFactoryInterface::class);
        $expressionFactory->method('create')->willReturn($expression);

        $parameterFactory = $this->createMock(ParameterFactoryInterface::class);
        $parameterFactory->method('create')->willReturn($parameters);

        return new DoctrineOrmFilterHandler($expressionFactory, $parameterFactory);
    }

    private function createFilterMock(): MockObject&FilterInterface
    {
        return $this->createMock(FilterInterface::class);
    }

    private function createFilterConfigMock(): FilterConfigInterface&MockObject
    {
        $filterConfig = $this->createMock(FilterConfigInterface::class);
        $filterConfig->method('getSupportedOperators')->willReturn([Operator::Equals]);

        return $filterConfig;
    }

    private function createFilterDataMock(): FilterData&MockObject
    {
        $filterData = $this->createMock(FilterData::class);
        $filterData->method('getOperator')->willReturn(Operator::Equals);

        return $filterData;
    }

    private function createDoctrineOrmProxyQueryMock(): MockObject&DoctrineOrmProxyQueryInterface
    {
        return $this->createMock(DoctrineOrmProxyQueryInterface::class);
    }

    private function createQueryBuilderMock(): MockObject&QueryBuilder
    {
        return $this->createMock(QueryBuilder::class);
    }

    private function createEventDispatcherMock(): MockObject&EventDispatcherInterface
    {
        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $eventDispatcher->method('hasListeners')->willReturn(true);

        return $eventDispatcher;
    }
}

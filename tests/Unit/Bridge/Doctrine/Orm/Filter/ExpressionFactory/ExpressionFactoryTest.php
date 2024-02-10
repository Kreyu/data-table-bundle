<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\ExpressionFactory;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\Query\Expr\Func;
use Doctrine\ORM\Query\Parameter;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionFactory\ExpressionFactory;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\AliasResolverInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterConfigInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ExpressionFactoryTest extends TestCase
{
    private MockObject&FilterInterface $filter;
    private MockObject&DoctrineOrmProxyQueryInterface $query;
    private MockObject&AliasResolverInterface $aliasResolver;

    protected function setUp(): void
    {
        $this->filter = $this->createMock(FilterInterface::class);
        $this->query = $this->createMock(DoctrineOrmProxyQueryInterface::class);
        $this->aliasResolver = $this->createMock(AliasResolverInterface::class);

        $this->query->method('getAliasResolver')->willReturn($this->aliasResolver);
    }

    #[DataProvider('expectedExpressionProvider')]
    public function testItCreatesExpression(string $queryPath, FilterData $data, array $parameters, mixed $expected): void
    {
        $this->aliasResolver->method('resolve')->willReturn($queryPath);

        $this->assertEquals($expected, $this->createExpression($data, $parameters));
    }

    public static function expectedExpressionProvider(): iterable
    {
        yield 'equals' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::Equals),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', '=', ':bar'),
        ];

        yield 'not equals' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::NotEquals),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', '<>', ':bar'),
        ];

        yield 'contains' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::Contains),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', 'LIKE', ':bar'),
        ];

        yield 'not contains' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::NotContains),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', 'NOT LIKE', ':bar'),
        ];

        yield 'in' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::In),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Func('foo IN', [':bar']),
        ];

        yield 'not in' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::NotIn),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Func('foo NOT IN', [':bar']),
        ];

        yield 'greater than' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::GreaterThan),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', '>', ':bar'),
        ];

        yield 'greater than or equals' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::GreaterThanEquals),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', '>=', ':bar'),
        ];

        yield 'less than' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::LessThan),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', '<', ':bar'),
        ];

        yield 'less than or equals' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::LessThanEquals),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', '<=', ':bar'),
        ];

        yield 'starts with' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::StartsWith),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', 'LIKE', ':bar'),
        ];

        yield 'ends with' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::EndsWith),
            'parameters' => [
                new Parameter('bar', null),
            ],
            'expected' => new Comparison('foo', 'LIKE', ':bar'),
        ];

        yield 'between' => [
            'query_path' => 'foo',
            'data' => new FilterData(null, Operator::Between),
            'parameters' => [
                'from' => new Parameter('bar', null),
                'to' => new Parameter('baz', null),
            ],
            'expected' => 'foo BETWEEN :bar AND :baz',
        ];
    }

    public function testItRequiresParameters()
    {
        $this->expectExceptionObject(new InvalidArgumentException('The expression factory requires at least one parameter.'));

        $this->createExpression();
    }

    public function testBetweenOperatorRequiresFromAndToParameters()
    {
        $this->expectExceptionObject(new InvalidArgumentException('Operator "between" requires "from" and "to" parameters.'));

        $this->createExpression(new FilterData(operator: Operator::Between), [
            new Parameter('foo', null),
            new Parameter('bar', null),
        ]);
    }

    public function testDataWithoutOperatorUsesFilterDefaultOperator()
    {
        $filterConfig = $this->createMock(FilterConfigInterface::class);
        $filterConfig->method('getDefaultOperator')->willReturn(Operator::NotEquals);

        $this->filter->method('getConfig')->willReturn($filterConfig);
        $this->aliasResolver->method('resolve')->willReturn('foo');

        $expression = $this->createExpression(new FilterData(), [
            new Parameter('bar', null),
        ]);

        $this->assertEquals(new Comparison('foo', '<>', ':bar'), $expression);
    }

    private function createExpression(FilterData $data = new FilterData(), array $parameters = []): mixed
    {
        return (new ExpressionFactory())->create($this->query, $data, $this->filter, $parameters);
    }
}

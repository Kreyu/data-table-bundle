<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\ParameterFactory;

use Doctrine\ORM\Query\Parameter;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ParameterFactory\ParameterFactory;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ParameterFactoryTest extends TestCase
{
    private MockObject&FilterInterface $filter;
    private MockObject&DoctrineOrmProxyQueryInterface $query;

    protected function setUp(): void
    {
        $this->filter = $this->createMock(FilterInterface::class);
        $this->query = $this->createMock(DoctrineOrmProxyQueryInterface::class);
    }

    public static function expectedParametersProvider(): iterable
    {
        yield 'equals' => [
            'data' => new FilterData('foo', Operator::Equals),
            'expected' => [
                new Parameter('_0', 'foo'),
            ],
        ];

        yield 'not equals' => [
            'data' => new FilterData('foo', Operator::NotEquals),
            'expected' => [
                new Parameter('_0', 'foo'),
            ],
        ];

        yield 'contains' => [
            'data' => new FilterData('foo', Operator::Contains),
            'expected' => [
                new Parameter('_0', '%foo%'),
            ],
        ];

        yield 'not contains' => [
            'data' => new FilterData('foo', Operator::NotContains),
            'expected' => [
                new Parameter('_0', '%foo%'),
            ],
        ];

        yield 'in' => [
            'data' => new FilterData('foo', Operator::In),
            'expected' => [
                new Parameter('_0', 'foo'),
            ],
        ];

        yield 'not in' => [
            'data' => new FilterData('foo', Operator::NotIn),
            'expected' => [
                new Parameter('_0', 'foo'),
            ],
        ];

        yield 'greater than' => [
            'data' => new FilterData('foo', Operator::GreaterThan),
            'expected' => [
                new Parameter('_0', 'foo'),
            ],
        ];

        yield 'greater than or equals' => [
            'data' => new FilterData('foo', Operator::GreaterThanEquals),
            'expected' => [
                new Parameter('_0', 'foo'),
            ],
        ];

        yield 'less than' => [
            'data' => new FilterData('foo', Operator::LessThan),
            'expected' => [
                new Parameter('_0', 'foo'),
            ],
        ];

        yield 'less than or equals' => [
            'data' => new FilterData('foo', Operator::LessThanEquals),
            'expected' => [
                new Parameter('_0', 'foo'),
            ],
        ];

        yield 'starts with' => [
            'data' => new FilterData('foo', Operator::StartsWith),
            'expected' => [
                new Parameter('_0', 'foo%'),
            ],
        ];

        yield 'ends with' => [
            'data' => new FilterData('foo', Operator::EndsWith),
            'expected' => [
                new Parameter('_0', '%foo'),
            ],
        ];

        yield 'between with "from" and "to" in value' => [
            'data' => new FilterData(['from' => 'foo', 'to' => 'bar'], Operator::Between),
            'expected' => [
                'from' => new Parameter('_0_from', 'foo'),
                'to' => new Parameter('_0_to', 'bar'),
            ],
        ];

        yield 'between with "from" only in value' => [
            'data' => new FilterData(['from' => 'foo'], Operator::Between),
            'expected' => [
                'from' => new Parameter('_0_from', 'foo'),
            ],
        ];

        yield 'between with "to" only in value' => [
            'data' => new FilterData(['to' => 'foo'], Operator::Between),
            'expected' => [
                'to' => new Parameter('_0_to', 'foo'),
            ],
        ];

        yield 'between without neither "from" or "to" in value' => [
            'data' => new FilterData([], Operator::Between),
            'expected' => [],
        ];
    }

    public function testItCreatesParametersWithFilterNameAndUniqueParameterId()
    {
        $this->filter->method('getName')->willReturn('foo');
        $this->query->method('getUniqueParameterId')->willReturn(10);

        $parameters = $this->createParameters();

        $this->assertEquals('foo_10', $parameters[0]->getName());
    }

    #[DataProvider('expectedParametersProvider')]
    public function testItCreatesParametersBasedOnFilterData(FilterData $data, array $expected): void
    {
        $this->assertEquals($expected, $this->createParameters($data));
    }

    private function createParameters(FilterData $data = new FilterData()): array
    {
        return (new ParameterFactory())->create($this->query, $data, $this->filter);
    }
}

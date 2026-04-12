<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\RowGroup;

use Kreyu\Bundle\DataTableBundle\RowGroup\AggregationCalculator;
use Kreyu\Bundle\DataTableBundle\RowGroup\AggregationDefinition;
use Kreyu\Bundle\DataTableBundle\RowGroup\AggregationType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;

class AggregationCalculatorTest extends TestCase
{
    private AggregationCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = new AggregationCalculator(PropertyAccess::createPropertyAccessor());
    }

    public function testCountAggregation(): void
    {
        $rows = [
            ['name' => 'Alice', 'salary' => 5000],
            ['name' => 'Bob', 'salary' => 6000],
            ['name' => 'Charlie', 'salary' => 7000],
        ];

        $definitions = [
            new AggregationDefinition('salary', AggregationType::Count, 'Employees', '[salary]'),
        ];

        $results = $this->calculator->calculate($rows, $definitions);

        $this->assertCount(1, $results);
        $this->assertSame(3, $results[0]->getValue());
        $this->assertSame('Employees', $results[0]->getLabel());
        $this->assertSame('salary', $results[0]->getColumnName());
    }

    public function testSumAggregation(): void
    {
        $rows = [
            ['name' => 'Alice', 'salary' => 5000],
            ['name' => 'Bob', 'salary' => 6000],
            ['name' => 'Charlie', 'salary' => 7000],
        ];

        $definitions = [
            new AggregationDefinition('salary', AggregationType::Sum, 'Total Salary', '[salary]'),
        ];

        $results = $this->calculator->calculate($rows, $definitions);

        $this->assertSame(18000.0, $results[0]->getValue());
    }

    public function testAverageAggregation(): void
    {
        $rows = [
            ['name' => 'Alice', 'salary' => 4000],
            ['name' => 'Bob', 'salary' => 6000],
            ['name' => 'Charlie', 'salary' => 8000],
        ];

        $definitions = [
            new AggregationDefinition('salary', AggregationType::Average, propertyPath: '[salary]'),
        ];

        $results = $this->calculator->calculate($rows, $definitions);

        $this->assertSame(6000.0, $results[0]->getValue());
    }

    public function testMinMaxAggregation(): void
    {
        $rows = [
            ['salary' => 5000],
            ['salary' => 3000],
            ['salary' => 8000],
        ];

        $definitions = [
            new AggregationDefinition('salary', AggregationType::Min, 'Min Salary', '[salary]'),
            new AggregationDefinition('salary', AggregationType::Max, 'Max Salary', '[salary]'),
        ];

        $results = $this->calculator->calculate($rows, $definitions);

        $this->assertSame(3000.0, $results[0]->getValue());
        $this->assertSame(8000.0, $results[1]->getValue());
    }

    public function testEmptyRows(): void
    {
        $definitions = [
            new AggregationDefinition('salary', AggregationType::Sum, propertyPath: '[salary]'),
        ];

        $results = $this->calculator->calculate([], $definitions);

        $this->assertCount(1, $results);
        $this->assertNull($results[0]->getValue());
    }

    public function testMultipleAggregations(): void
    {
        $rows = [
            ['salary' => 1000, 'bonus' => 100],
            ['salary' => 2000, 'bonus' => 200],
            ['salary' => 3000, 'bonus' => 300],
        ];

        $definitions = [
            new AggregationDefinition('salary', AggregationType::Count, 'Count', '[salary]'),
            new AggregationDefinition('salary', AggregationType::Sum, 'Total Salary', '[salary]'),
            new AggregationDefinition('bonus', AggregationType::Average, 'Avg Bonus', '[bonus]'),
        ];

        $results = $this->calculator->calculate($rows, $definitions);

        $this->assertCount(3, $results);
        $this->assertSame(3, $results[0]->getValue());
        $this->assertSame(6000.0, $results[1]->getValue());
        $this->assertSame(200.0, $results[2]->getValue());
    }

    public function testCustomPropertyPath(): void
    {
        $rows = [
            ['details' => ['salary' => 5000]],
            ['details' => ['salary' => 6000]],
        ];

        $definitions = [
            new AggregationDefinition('salary', AggregationType::Sum, propertyPath: '[details][salary]'),
        ];

        $results = $this->calculator->calculate($rows, $definitions);

        $this->assertSame(11000.0, $results[0]->getValue());
    }
}

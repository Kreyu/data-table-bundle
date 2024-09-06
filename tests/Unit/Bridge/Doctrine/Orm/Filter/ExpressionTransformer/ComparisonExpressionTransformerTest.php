<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\ExpressionTransformer;

use Doctrine\ORM\Query\Expr\Comparison;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\AbstractComparisonExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use PHPUnit\Framework\TestCase;

class ComparisonExpressionTransformerTest extends TestCase
{
    private AbstractComparisonExpressionTransformer $transformer;

    protected function setUp(): void
    {
        $this->transformer = new class extends AbstractComparisonExpressionTransformer {};
    }

    public function testItThrowsExceptionWhenExpressionIsNotComparison(): void
    {
        $expression = 'foo = bar';

        $this->expectExceptionObject(new UnexpectedTypeException($expression, Comparison::class));

        $this->transformer->transform($expression);
    }

    public function testItDoesNotModifyExpressionByDefault(): void
    {
        $comparison = new Comparison('foo', '=', 'bar');

        $this->assertEquals($comparison, $this->transformer->transform($comparison));
    }
}

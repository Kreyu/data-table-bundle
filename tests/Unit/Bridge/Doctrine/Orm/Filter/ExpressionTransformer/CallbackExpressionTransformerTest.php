<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\ExpressionTransformer;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\CallbackExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\ExpressionTransformerInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Test\ExpressionTransformerTestCase;

class CallbackExpressionTransformerTest extends ExpressionTransformerTestCase
{
    public static function createTransformer(?callable $callback = null): ExpressionTransformerInterface
    {
        return new CallbackExpressionTransformer($callback ?? static fn (mixed $expression): mixed => $expression);
    }

    public static function expressionTransformationProvider(): iterable
    {
        $expr = new Expr();

        yield [self::createTransformer(trim(...)), ' foo ', 'foo'];

        yield [self::createTransformer(static fn ($expression) => "{$expression} bar"), 'foo', 'foo bar'];

        yield [
            self::createTransformer(static fn (Expr\Comparison $comparison) => $expr->eq(
                $expr->lower($comparison->getLeftExpr()),
                $expr->upper($comparison->getRightExpr()),
            )),
            $expr->eq('foo', 'bar'),
            $expr->eq($expr->lower('foo'), $expr->upper('bar')),
        ];
    }

    public function testConstructorConvertsCallableToClosure(): void
    {
        $transformer = self::createTransformer(static fn () => null);

        $reflectionProperty = new \ReflectionProperty($transformer, 'callback');

        $this->assertSame('Closure', (string) $reflectionProperty->getType());
    }
}

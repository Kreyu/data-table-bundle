<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Filter\ExpressionTransformer;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\ExpressionTransformerInterface;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\TrimExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Test\ExpressionTransformerTestCase;

class TrimExpressionTransformerTest extends ExpressionTransformerTestCase
{
    public static function createTransformer(bool $transformLeftExpr = true, bool $transformRightExpr = true): ExpressionTransformerInterface
    {
        return new TrimExpressionTransformer($transformLeftExpr, $transformRightExpr);
    }

    public static function expressionTransformationProvider(): iterable
    {
        $expr = new Expr();

        yield [self::createTransformer(), $expr->eq('a', 'b'), $expr->eq($expr->trim('a'), $expr->trim('b'))];

        yield [self::createTransformer(false), $expr->eq('a', 'b'), $expr->eq('a', $expr->trim('b'))];

        yield [self::createTransformer(true, false), $expr->eq('a', 'b'), $expr->eq($expr->trim('a'), 'b')];

        yield [self::createTransformer(false, false), $expr->eq('a', 'b'), $expr->eq('a', 'b')];
    }
}

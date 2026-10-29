<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Fixtures\Filter\ExpressionTransformer;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer\AbstractComparisonExpressionTransformer;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class CustomExpressionTransformer extends AbstractComparisonExpressionTransformer
{
    protected function transformLeftExpr(mixed $leftExpr): string
    {
        return $this->wrap($leftExpr);
    }

    protected function transformRightExpr(mixed $rightExpr): string
    {
        return $this->wrap($rightExpr);
    }

    private function wrap(mixed $expression): string
    {
        if ($expression instanceof \Stringable) {
            $expression = (string) $expression;
        }

        $isString = \is_string($expression);

        if (!$isString) {
            throw new UnexpectedTypeException($expression, 'string|\Stringable');
        }

        return sprintf('CUSTOM(%s)', $expression);
    }
}

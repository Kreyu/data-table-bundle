<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

abstract class AbstractComparisonExpressionTransformer implements ExpressionTransformerInterface
{
    public function __construct(
        private readonly bool $transformLeftExpr = true,
        private readonly bool $transformRightExpr = true,
    ) {
    }

    public function transform(mixed $expression): Comparison
    {
        if (!$expression instanceof Comparison) {
            throw new UnexpectedTypeException($expression, Comparison::class);
        }

        $leftExpr = $expression->getLeftExpr();
        $rightExpr = $expression->getRightExpr();

        if ($this->transformLeftExpr) {
            $leftExpr = $this->transformLeftExpr($leftExpr);
        }

        if ($this->transformRightExpr) {
            $rightExpr = $this->transformRightExpr($rightExpr);
        }

        return new Comparison($leftExpr, $expression->getOperator(), $rightExpr);
    }

    protected function transformLeftExpr(mixed $leftExpr): mixed
    {
        return $leftExpr;
    }

    protected function transformRightExpr(mixed $rightExpr): mixed
    {
        return $rightExpr;
    }

    protected function getExpressionBuilder(): Expr
    {
        return new Expr();
    }
}

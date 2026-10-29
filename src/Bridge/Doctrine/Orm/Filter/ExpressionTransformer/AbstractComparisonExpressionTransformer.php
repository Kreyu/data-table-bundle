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
    ) {}

    public function transform(mixed $expression): Comparison
    {
        if (!$expression instanceof Comparison) {
            throw new UnexpectedTypeException($expression, Comparison::class);
        }

        return new Comparison(
            $this->getLeftExpression($expression),
            $expression->getOperator(),
            $this->getRightExpression($expression),
        );
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

    private function getLeftExpression(Comparison $expression): mixed
    {
        if (!$this->transformLeftExpr) {
            return $expression->getLeftExpr();
        }

        return $this->transformLeftExpr($expression->getLeftExpr());
    }

    private function getRightExpression(Comparison $expression): mixed
    {
        if (!$this->transformRightExpr) {
            return $expression->getRightExpr();
        }

        return $this->transformRightExpr($expression->getRightExpr());
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionTransformer;

use Doctrine\ORM\Query\Expr;

class TrimExpressionTransformer extends AbstractComparisonExpressionTransformer
{
    protected function transformLeftExpr(mixed $leftExpr): Expr\Func
    {
        return $this->getExpressionBuilder()->trim($leftExpr);
    }

    protected function transformRightExpr(mixed $rightExpr): Expr\Func
    {
        return $this->getExpressionBuilder()->trim($rightExpr);
    }
}

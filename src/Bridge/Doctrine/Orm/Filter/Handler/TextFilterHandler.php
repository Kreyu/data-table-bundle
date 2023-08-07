<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Handler;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Filter\Handler\FilterTypeHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

/**
 * @template-extends FilterTypeHandlerInterface<QueryBuilder>
 */
class TextFilterHandler extends AbstractFilterTypeHandler
{
    protected function getExpression(Operator $operator): callable
    {
        $expr = new Expr();

        return match ($operator) {
            Operator::EQUALS => $expr->eq(...),
            Operator::NOT_EQUALS => $expr->neq(...),
            Operator::CONTAINS, Operator::STARTS_WITH, Operator::ENDS_WITH => $expr->like(...),
            Operator::NOT_CONTAINS => $expr->notLike(...),
            default => throw new \InvalidArgumentException('Operator not supported'),
        };
    }

    protected function getValueExpression(Operator $operator, mixed $value): string
    {
        return (string) match ($operator) {
            Operator::CONTAINS, Operator::NOT_CONTAINS => "%$value%",
            Operator::STARTS_WITH => "$value%",
            Operator::ENDS_WITH => "%$value",
            default => $value,
        };
    }

    protected function getComparisonExpression(Operator $operator, string $x, string $y): Expr\Comparison
    {
        $operatorName = match ($operator) {
            Operator::EQUALS => 'eq',
            Operator::NOT_EQUALS => 'neq',
            Operator::CONTAINS, Operator::STARTS_WITH, Operator::ENDS_WITH => 'like',
            Operator::NOT_CONTAINS => 'notLike',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };

        return new Expr\Comparison($x, $operatorName, $y);
    }
}

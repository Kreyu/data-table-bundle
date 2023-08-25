<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class StringFilterType extends AbstractFilterType
{
    protected function getOperatorExpression(string $queryPath, string $parameterName, Operator $operator, Expr $expr): object
    {
        $expression = match ($operator) {
            Operator::Equal => $expr->eq(...),
            Operator::NotEqual => $expr->neq(...),
            Operator::Contain, Operator::StartWith, Operator::EndWith => $expr->like(...),
            Operator::NotContain => $expr->notLike(...),
            default => throw new InvalidArgumentException('Operator not supported'),
        };

        return $expression($queryPath, ":$parameterName");
    }

    protected function getParameterValue(Operator $operator, mixed $value): string
    {
        return (string) match ($operator) {
            Operator::Contain, Operator::NotContain => "%$value%",
            Operator::StartWith => "$value%",
            Operator::EndWith => "%$value",
            default => $value,
        };
    }
}

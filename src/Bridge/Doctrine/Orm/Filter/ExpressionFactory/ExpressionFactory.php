<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\ExpressionFactory;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class ExpressionFactory implements ExpressionFactoryInterface
{
    public function create(DoctrineOrmProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $parameters): mixed
    {
        if (empty($parameters)) {
            throw new InvalidArgumentException('The expression factory requires at least one parameter.');
        }

        $queryPath = $query->getAliasResolver()->resolve($filter->getQueryPath(), $query->getQueryBuilder());

        $operator = $data->getOperator() ?? $filter->getConfig()->getDefaultOperator();

        $expr = new Expr();

        if (Operator::Between === $operator) {
            $parameterFrom = $parameters['from'] ?? null;
            $parameterTo = $parameters['to'] ?? null;

            if ($parameterFrom && $parameterTo) {
                return $expr->between($queryPath, ':'.$parameterFrom->getName(), ':'.$parameterTo->getName());
            }

            throw new InvalidArgumentException('Operator "between" requires "from" and "to" parameters.');
        }

        $exprMethod = match ($operator) {
            Operator::Equals => $expr->eq(...),
            Operator::NotEquals => $expr->neq(...),
            Operator::GreaterThan => $expr->gt(...),
            Operator::GreaterThanEquals => $expr->gte(...),
            Operator::LessThan => $expr->lt(...),
            Operator::LessThanEquals => $expr->lte(...),
            Operator::Contains, Operator::StartsWith, Operator::EndsWith => $expr->like(...),
            Operator::NotContains => $expr->notLike(...),
            Operator::In => $expr->in(...),
            Operator::NotIn => $expr->notIn(...),
        };

        $parameter = $parameters[array_key_first($parameters)];

        return $exprMethod($queryPath, ':'.$parameter->getName());
    }
}

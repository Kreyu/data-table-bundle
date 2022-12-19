<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;

class StringFilter extends AbstractFilter
{
    protected function filter(ProxyQueryInterface $query, FilterData $data): void
    {
        $value = $data->getValue();
        $operator = $data->getOperator() ?? Operator::EQUAL;

        $exprMethod = match ($operator) {
            Operator::GREATER_EQUAL => 'gte',
            Operator::GREATER_THAN => 'gt',
            Operator::EQUAL => 'eq',
            Operator::LESS_EQUAL => 'lte',
            Operator::LESS_THAN => 'lt',
            Operator::CONTAINS => 'like',
            Operator::NOT_CONTAINS => 'notLike',
            Operator::NOT_EQUAL => 'neq',
        };

        $parameterValue = match ($operator) {
            Operator::CONTAINS, Operator::NOT_CONTAINS => "%$value%",
            default => $value,
        };

        $parameterName = $this->generateUniqueParameterName($query);

        $expression = $query->expr()->{$exprMethod}($this->getFieldName(), ":$parameterName");

        $query
            ->andWhere($expression)
            ->setParameter($parameterName, $parameterValue);
    }

    protected function getSupportedOperators(): array
    {
        return [
            Operator::GREATER_EQUAL,
            Operator::GREATER_THAN,
            Operator::EQUAL,
            Operator::LESS_EQUAL,
            Operator::LESS_THAN,
            Operator::CONTAINS,
            Operator::NOT_CONTAINS,
            Operator::NOT_EQUAL,
        ];
    }
}

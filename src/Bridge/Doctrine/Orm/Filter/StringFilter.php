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
            Operator::EQUAL => 'eq',
            Operator::NOT_EQUAL => 'neq',
            Operator::CONTAINS, Operator::STARTS_WITH, Operator::ENDS_WITH => 'like',
            Operator::NOT_CONTAINS => 'notLike',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };

        $parameterValue = match ($operator) {
            Operator::CONTAINS, Operator::NOT_CONTAINS => "%$value%",
            Operator::STARTS_WITH => "%$value",
            Operator::ENDS_WITH => "$value%",
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
            Operator::EQUAL,
            Operator::NOT_EQUAL,
            Operator::CONTAINS,
            Operator::NOT_CONTAINS,
            Operator::STARTS_WITH,
            Operator::ENDS_WITH,
        ];
    }
}

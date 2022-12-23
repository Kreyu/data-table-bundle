<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class NumericFilter extends AbstractFilter
{
    public function getFormOptions(): array
    {
        return array_merge(parent::getFormOptions(), [
            'field_type' => NumberType::class,
        ]);
    }

    protected function filter(ProxyQueryInterface $query, FilterData $data): void
    {
        $value = $data->getValue();
        $operator = $data->getOperator() ?? Operator::EQUAL;

        $exprMethod = match ($operator) {
            Operator::EQUAL => 'eq',
            Operator::NOT_EQUAL => 'neq',
            Operator::GREATER_EQUAL => 'gte',
            Operator::GREATER_THAN => 'gt',
            Operator::LESS_EQUAL => 'lte',
            Operator::LESS_THAN => 'lt',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };

        $parameterName = $this->generateUniqueParameterName($query);

        $expression = $query->expr()->{$exprMethod}($this->getFieldName(), ":$parameterName");

        $query
            ->andWhere($expression)
            ->setParameter($parameterName, $value);
    }

    protected function getSupportedOperators(): array
    {
        return [
            Operator::EQUAL,
            Operator::NOT_EQUAL,
            Operator::GREATER_EQUAL,
            Operator::GREATER_THAN,
            Operator::LESS_EQUAL,
            Operator::LESS_THAN,
        ];
    }
}

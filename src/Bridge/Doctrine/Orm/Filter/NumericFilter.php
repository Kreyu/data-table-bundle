<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumericFilter extends AbstractFilter
{
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('field_type', NumberType::class);
    }

    protected function filter(ProxyQueryInterface $query, FilterData $data): void
    {
        $operator = $data->getOperator() ?? Operator::EQUAL;
        $value = $data->getValue();

        $expressionBuilderMethodName = $this->getExpressionBuilderMethodName($operator);

        $parameterName = $this->generateUniqueParameterName($query);

        $expression = $query->expr()->{$expressionBuilderMethodName}($this->getFieldName(), ":$parameterName");

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

    private function getExpressionBuilderMethodName(Operator $operator): string
    {
        return match ($operator) {
            Operator::EQUAL => 'eq',
            Operator::NOT_EQUAL => 'neq',
            Operator::GREATER_EQUAL => 'gte',
            Operator::GREATER_THAN => 'gt',
            Operator::LESS_EQUAL => 'lte',
            Operator::LESS_THAN => 'lt',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };
    }
}

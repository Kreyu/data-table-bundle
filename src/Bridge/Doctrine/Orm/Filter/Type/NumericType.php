<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface as DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumericType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('field_type', NumberType::class);
    }

    protected function filter(DoctrineOrmProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        $operator = $data->getOperator() ?? Operator::EQUALS;
        $value = $data->getValue();

        try {
            $expressionBuilderMethodName = $this->getExpressionBuilderMethodName($operator);
        } catch (\InvalidArgumentException) {
            return;
        }

        $parameterName = $this->generateUniqueParameterName($query, $filter);

        $expression = $query->expr()->{$expressionBuilderMethodName}($filter->getQueryPath(), ":$parameterName");

        $query
            ->andWhere($expression)
            ->setParameter($parameterName, $value);
    }

    private function getExpressionBuilderMethodName(Operator $operator): string
    {
        return match ($operator) {
            Operator::EQUALS => 'eq',
            Operator::NOT_EQUALS => 'neq',
            Operator::GREATER_THAN_EQUALS => 'gte',
            Operator::GREATER_THAN => 'gt',
            Operator::LESS_THAN_EQUALS => 'lte',
            Operator::LESS_THAN => 'lt',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityFilter extends AbstractFilter
{
    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('field_type', EntityType::class);
    }

    protected function filter(ProxyQueryInterface $query, FilterData $data): void
    {
        $operator = $data->getOperator() ?? Operator::EQUAL;
        $value = (array) $data->getValue();

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
            Operator::CONTAINS,
            Operator::NOT_CONTAINS,
        ];
    }

    private function getExpressionBuilderMethodName(Operator $operator): string
    {
        return match ($operator) {
            Operator::EQUAL, Operator::CONTAINS => 'in',
            Operator::NOT_EQUAL, Operator::NOT_CONTAINS => 'notIn',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };
    }
}

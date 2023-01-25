<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface as DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('operator_options', [
            'visible' => false,
            'choices' => [
                Operator::EQUALS,
                Operator::NOT_EQUALS,
                Operator::CONTAINS,
                Operator::NOT_CONTAINS,
                Operator::STARTS_WITH,
                Operator::ENDS_WITH,
            ],
        ]);
    }

    protected function filter(DoctrineOrmProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        $operator = $data->getOperator() ?? Operator::EQUALS;
        $value = $data->getValue();

        $expressionBuilderMethodName = $this->getExpressionBuilderMethodName($operator);

        $parameterName = $this->generateUniqueParameterName($query, $filter);

        $expression = $query->expr()->{$expressionBuilderMethodName}($filter->getQueryPath(), ":$parameterName");

        $query
            ->andWhere($expression)
            ->setParameter($parameterName, $this->getParameterValue($operator, $value));
    }

    private function getExpressionBuilderMethodName(Operator $operator): string
    {
        return match ($operator) {
            Operator::EQUALS => 'eq',
            Operator::NOT_EQUALS => 'neq',
            Operator::CONTAINS, Operator::STARTS_WITH, Operator::ENDS_WITH => 'like',
            Operator::NOT_CONTAINS => 'notLike',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };
    }

    private function getParameterValue(Operator $operator, mixed $value): string
    {
        return (string) match ($operator) {
            Operator::CONTAINS, Operator::NOT_CONTAINS => "%$value%",
            Operator::STARTS_WITH => "$value%",
            Operator::ENDS_WITH => "%$value",
            default => $value,
        };
    }
}

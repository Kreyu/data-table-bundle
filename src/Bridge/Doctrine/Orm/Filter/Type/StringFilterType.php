<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringFilterType extends AbstractFilterType
{
    /**
     * @param DoctrineOrmProxyQuery $query
     */
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        $operator = $data->getOperator() ?? Operator::EQUALS;
        $value = $data->getValue();

        try {
            $expressionBuilderMethodName = $this->getExpressionBuilderMethodName($operator);
        } catch (\InvalidArgumentException) {
            return;
        }

        $parameterName = $this->getUniqueParameterName($query, $filter);

        $expression = $query->expr()->{$expressionBuilderMethodName}($this->getFilterQueryPath($query, $filter), ":$parameterName");

        $query
            ->andWhere($expression)
            ->setParameter($parameterName, $this->getParameterValue($operator, $value));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('operator_options', function (OptionsResolver $resolver) {
            $resolver->setDefaults([
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
        });
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

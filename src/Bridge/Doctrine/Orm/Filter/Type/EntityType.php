<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface as BaseProxyQueryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as EntityFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityType extends AbstractType
{
    /**
     * @param ProxyQueryInterface $query
     */
    public function apply(BaseProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        $operator = $data->getOperator() ?? Operator::EQUALS;
        $value = (array) $data->getValue();

        try {
            $expressionBuilderMethodName = $this->getExpressionBuilderMethodName($operator);
        } catch (\InvalidArgumentException) {
            return;
        }

        $parameterName = $this->getUniqueParameterName($query, $filter);

        $expression = $query->expr()->{$expressionBuilderMethodName}($filter->getQueryPath(), ":$parameterName");

        $query
            ->andWhere($expression)
            ->setParameter($parameterName, $value);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'field_type' => EntityFormType::class,
            'operator_options' => [
                'visible' => false,
                'choices' => [
                    Operator::EQUALS,
                    Operator::NOT_EQUALS,
                    Operator::CONTAINS,
                    Operator::NOT_CONTAINS,
                ],
            ],
        ]);
    }

    private function getExpressionBuilderMethodName(Operator $operator): string
    {
        return match ($operator) {
            Operator::EQUALS, Operator::CONTAINS => 'in',
            Operator::NOT_EQUALS, Operator::NOT_CONTAINS => 'notIn',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };
    }
}

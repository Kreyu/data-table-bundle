<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQueryInterface as DoctrineOrmProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Bridge\Doctrine\Form\Type\EntityType as EntityFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityType extends AbstractType
{
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

    protected function filter(DoctrineOrmProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void
    {
        $operator = $data->getOperator() ?? Operator::EQUALS;
        $value = (array) $data->getValue();

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
            Operator::EQUALS, Operator::CONTAINS => 'in',
            Operator::NOT_EQUALS, Operator::NOT_CONTAINS => 'notIn',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };
    }
}

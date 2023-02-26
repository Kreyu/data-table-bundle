<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DateType extends AbstractType
{
    /**
     * @param DoctrineOrmProxyQuery $query
     */
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        $operator = $data->getOperator() ?? Operator::EQUALS;
        $value = $data->getValue();

        if (is_string($value)) {
            $dateTime = \DateTime::createFromFormat('Y-m-d', $value);
        } else {
            $dateTime = new \DateTime();
            $dateTime->setDate((int) $value['year'] ?: 0, (int) $value['month'] ?: 0, (int) $value['day'] ?: 0);
        }

        $dateTime->setTime(0, 0);

        try {
            $expressionBuilderMethodName = $this->getExpressionBuilderMethodName($operator);
        } catch (\InvalidArgumentException) {
            return;
        }

        $parameterName = $this->getUniqueParameterName($query, $filter);

        $expression = $query->expr()->{$expressionBuilderMethodName}($this->getFilterQueryPath($query, $filter), ":$parameterName");

        $query
            ->andWhere($expression)
            ->setParameter($parameterName, $dateTime);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('field_type', Form\DateType::class);

        $resolver->setDefault('operator_options', function (OptionsResolver $resolver) {
            $resolver->setDefaults([
                'visible' => false,
                'choices' => [
                    Operator::EQUALS,
                    Operator::NOT_EQUALS,
                    Operator::GREATER_THAN,
                    Operator::GREATER_THAN_EQUALS,
                    Operator::LESS_THAN,
                    Operator::LESS_THAN_EQUALS,
                ],
            ]);
        });

        $resolver->setDefault('active_filter_formatter', function (FilterData $data, array $options): mixed {
            $value = $data->getValue();

            if ($value instanceof \DateTimeInterface) {
                return $value->format($options['field_options']['input_format'] ?? 'Y-m-d');
            }

            return $value;
        });
    }

    private function getExpressionBuilderMethodName(Operator $operator): string
    {
        return match ($operator) {
            Operator::EQUALS => 'eq',
            Operator::NOT_EQUALS => 'neq',
            Operator::GREATER_THAN => 'gt',
            Operator::GREATER_THAN_EQUALS => 'gte',
            Operator::LESS_THAN => 'lt',
            Operator::LESS_THAN_EQUALS => 'lte',
            default => throw new \InvalidArgumentException('Operator not supported'),
        };
    }
}

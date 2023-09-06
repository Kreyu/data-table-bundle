<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumericFilterType extends AbstractDoctrineOrmFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'form_type' => NumberType::class,
                'supported_operators' => [
                    Operator::Equals,
                    Operator::NotEquals,
                    Operator::GreaterThanEquals,
                    Operator::GreaterThan,
                    Operator::LessThanEquals,
                    Operator::LessThan,
                ],
            ])
        ;
    }

    protected function getOperatorExpression(string $queryPath, string $parameterName, Operator $operator, Expr $expr): object
    {
        $expression = match ($operator) {
            Operator::Equals => $expr->eq(...),
            Operator::NotEquals => $expr->neq(...),
            Operator::GreaterThanEquals => $expr->gte(...),
            Operator::GreaterThan => $expr->gt(...),
            Operator::LessThanEquals => $expr->lte(...),
            Operator::LessThan => $expr->lt(...),
            default => throw new InvalidArgumentException('Operator not supported'),
        };

        return $expression($queryPath, ":$parameterName");
    }
}

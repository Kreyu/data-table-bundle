<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'supported_operators' => [
                    Operator::Equals,
                    Operator::NotEquals,
                    Operator::Contains,
                    Operator::NotContains,
                    Operator::StartsWith,
                    Operator::EndsWith,
                ],
            ])
        ;
    }

    protected function getOperatorExpression(string $queryPath, string $parameterName, Operator $operator, Expr $expr): object
    {
        $expression = match ($operator) {
            Operator::Equals => $expr->eq(...),
            Operator::NotEquals => $expr->neq(...),
            Operator::Contains, Operator::StartsWith, Operator::EndsWith => $expr->like(...),
            Operator::NotContains => $expr->notLike(...),
            default => throw new InvalidArgumentException('Operator not supported'),
        };

        return $expression($queryPath, ":$parameterName");
    }

    protected function getParameterValue(Operator $operator, mixed $value): string
    {
        return (string) match ($operator) {
            Operator::Contains, Operator::NotContains => "%$value%",
            Operator::StartsWith => "$value%",
            Operator::EndsWith => "%$value",
            default => $value,
        };
    }
}

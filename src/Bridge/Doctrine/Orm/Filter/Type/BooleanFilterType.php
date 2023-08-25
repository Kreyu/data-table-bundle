<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Doctrine\ORM\Query\Expr;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function Symfony\Component\Translation\t;

class BooleanFilterType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'value_form_type' => ChoiceType::class,
                'active_filter_formatter' => fn (FilterData $data) => t($data->getValue() ? 'Yes' : 'No', domain: 'KreyuDataTable'),
            ])
            ->addNormalizer('value_form_options', function (Options $options, mixed $value) {
                if (ChoiceType::class !== $options['value_form_type']) {
                    return $value;
                }

                return $value + [
                    'choices' => ['yes' => true, 'no' => false],
                    'choice_label' => function (bool $choice, string $key) {
                        return t(ucfirst($key), domain: 'KreyuDataTable');
                    },
                ];
            })
        ;
    }

    protected function getOperatorExpression(string $queryPath, string $parameterName, Operator $operator, Expr $expr): object
    {
        $expression = match ($operator) {
            Operator::Equal => $expr->eq(...),
            Operator::NotEqual => $expr->neq(...),
            default => throw new InvalidArgumentException('Operator not supported'),
        };

        return $expression($queryPath, ":$parameterName");
    }
}

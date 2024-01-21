<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NumericFilterType extends AbstractDoctrineOrmFilterType
{
    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $builder->setSupportedOperators([
            Operator::Equals,
            Operator::NotEquals,
            Operator::Contains,
            Operator::NotContains,
            Operator::StartsWith,
            Operator::EndsWith,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'form_type' => NumberType::class,
        ]);
    }
}

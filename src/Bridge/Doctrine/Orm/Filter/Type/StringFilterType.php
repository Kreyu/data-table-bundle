<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class StringFilterType extends AbstractDoctrineOrmFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'default_operator' => Operator::Contains,
            'supported_operators' => [
                Operator::Equals,
                Operator::NotEquals,
                Operator::Contains,
                Operator::NotContains,
                Operator::StartsWith,
                Operator::EndsWith,
            ],
        ]);
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Formatter\DateActiveFilterFormatter;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateFilterType extends AbstractDoctrineOrmFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'form_type' => DateType::class,
                'supported_operators' => [
                    Operator::Equals,
                    Operator::NotEquals,
                    Operator::GreaterThan,
                    Operator::GreaterThanEquals,
                    Operator::LessThan,
                    Operator::LessThanEquals,
                ],
                'active_filter_formatter' => new DateActiveFilterFormatter(),
            ])
            ->addNormalizer('form_options', function (Options $options, array $value) {
                if (DateType::class !== $options['form_type']) {
                    return $value;
                }

                return $value + ['widget' => 'single_text'];
            })
        ;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class BooleanFilterType extends AbstractDoctrineOrmFilterType
{
    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $builder->setSupportedOperators([
            Operator::Equals,
            Operator::NotEquals,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'form_type' => ChoiceType::class,
                'active_filter_formatter' => function (FilterData $data) {
                    return new TranslatableMessage($data->getValue() ? 'Yes' : 'No', domain: 'KreyuDataTable');
                },
            ])
            ->addNormalizer('form_options', function (Options $options, array $value): array {
                if (ChoiceType::class !== $options['form_type']) {
                    return $value;
                }

                return $value + [
                    'choices' => ['Yes' => true, 'No' => false],
                    'choice_translation_domain' => 'KreyuDataTable',
                ];
            })
        ;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('value', $options['form_type'], $options['form_options']);

        $builder->get('value')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            if ('' === $event->getData()) {
                $event->setData(null);
            }
        });

        if ($options['operator_selectable']) {
            $builder->add('operator', $options['operator_form_type'], $options['operator_form_options'] + [
                'empty_data' => $options['default_operator'],
                'choices' => $options['supported_operators'],
            ]);

            $builder->get('operator')->addViewTransformer(new CallbackTransformer(
                fn (mixed $value) => $value,
                fn (mixed $value) => $value instanceof Operator ? $value->value : $value,
            ));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'required' => false,
                'data_class' => FilterData::class,
                'form_type' => TextType::class,
                'form_options' => [],
                'operator_form_type' => OperatorType::class,
                'operator_form_options' => [],
                'supported_operators' => [],
                'operator_selectable' => false,
                'default_operator' => Operator::Equals,
                'empty_data' => function (Options $options) {
                    return new FilterData(operator: $options['default_operator']);
                },
            ])
            ->setAllowedTypes('form_type', 'string')
            ->setAllowedTypes('form_options', 'array')
            ->setAllowedTypes('operator_form_type', 'string')
            ->setAllowedTypes('operator_form_options', 'array')
            ->setAllowedTypes('operator_selectable', 'bool')
            ->setAllowedTypes('supported_operators', Operator::class.'[]')
            ->setAllowedTypes('default_operator', Operator::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_filter_data';
    }
}

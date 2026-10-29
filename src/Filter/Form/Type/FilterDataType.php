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
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var class-string<FormTypeInterface> $formType */
        $formType = $options['form_type'];

        /** @var array<string, mixed> $formOptions */
        $formOptions = $options['form_options'];

        $builder->add('value', $formType, $formOptions);

        $builder->get('value')->addEventListener(FormEvents::PRE_SUBMIT, static function (FormEvent $event) {
            if ('' === $event->getData()) {
                $event->setData(null);
            }
        });

        if ($options['operator_selectable']) {
            /** @var class-string<FormTypeInterface> $operatorFormType */
            $operatorFormType = $options['operator_form_type'];

            /** @var array<string, mixed> $operatorFormOptions */
            $operatorFormOptions = $options['operator_form_options'];

            $builder->add(
                'operator',
                $operatorFormType,
                $operatorFormOptions
                + [
                    'empty_data' => $options['default_operator'],
                    'choices' => $options['supported_operators'],
                ],
            );

            $builder->get('operator')->addViewTransformer(new CallbackTransformer(
                static fn (mixed $value) => $value,
                static fn (mixed $value) => $value instanceof Operator ? $value->value : $value,
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
                'empty_data' => static function (Options $options): FilterData {
                    /** @var Operator $defaultOperator */
                    $defaultOperator = $options['default_operator'];

                    return new FilterData(operator: $defaultOperator);
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

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('operator', $options['operator_type'], $options['operator_options'] + [
                'label' => false,
                'required' => false,
                'getter' => fn (FilterData $data) => $data->getOperator(),
                'setter' => fn (FilterData $data, Operator $operator) => $data->setOperator($operator),
            ])
            ->add('value', $options['field_type'], $options['field_options'] + [
                'label' => false,
                'required' => false,
                'empty_data' => '',
                'setter' => function (FilterData $data, $value, FormInterface $form) {
                    if ($form->getParent()->getName() === 'category') {
                        $data->setValue($value);
                    }
                },
            ])
        ;

        $builder->get('value')->addModelTransformer(new CallbackTransformer(
            fn (mixed $value) => $value,
            fn (mixed $value) => $value ?? '',
        ));

        $builder->get('operator')->addViewTransformer(new CallbackTransformer(
            fn (mixed $value) => $value,
            fn (mixed $value) => $value instanceof Operator ? $value->value : $value,
        ));
    }

    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        /** @var FilterData|null $data */
        $data = $view->vars['value'];

        if ($data && $data->hasValue() && $options['active_filter_formatter']) {
            $view->vars['value'] = $options['active_filter_formatter']($data, $options);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'data_class' => FilterData::class,
            'operator_type' => OperatorType::class,
            'operator_options' => [
                'visible' => false,
            ],
            'field_type' => TextType::class,
            'field_options' => [],
            'active_filter_formatter' => null,
        ]);

        $resolver->setAllowedTypes('operator_type', 'string');
        $resolver->setAllowedTypes('operator_options', 'array');

        $resolver->setAllowedTypes('field_type', 'string');
        $resolver->setAllowedTypes('field_options', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_filter_data';
    }
}

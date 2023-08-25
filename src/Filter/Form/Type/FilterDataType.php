<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterDataType extends AbstractType implements DataMapperInterface
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['operator_selectable'] = $options['operator_selectable'];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $operatorFormType = $options['operator_form_type'];
        $operatorFormOptions = $options['operator_form_options'];

        if (!$options['operator_selectable']) {
            $operatorFormType = HiddenType::class;
            $operatorFormOptions['data'] = $options['default_operator']->value;
        }

        if (is_a($operatorFormType, OperatorType::class, true)) {
            $operatorFormOptions['choices'] = $options['supported_operators'];
            $operatorFormOptions['empty_data'] = $options['default_operator'];
        }

        $builder
            ->add('value', $options['value_form_type'], $options['value_form_options'])
            ->add('operator', $operatorFormType, $operatorFormOptions)
            ->setDataMapper($this)
        ;
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


            'value_form_type' => TextType::class,
            'value_form_options' => [],
            'operator_form_type' => OperatorType::class,
            'operator_form_options' => [],
            'default_operator' => Operator::Equal,
            'supported_operators' => [],
            'operator_selectable' => false,
        ]);

        $resolver->setAllowedTypes('value_form_type', 'string');
        $resolver->setAllowedTypes('value_form_options', 'array');

        $resolver->setAllowedTypes('operator_form_type', 'string');
        $resolver->setAllowedTypes('operator_form_options', 'array');

        $resolver->setAllowedTypes('default_operator', Operator::class);
        $resolver->setAllowedTypes('supported_operators', Operator::class.'[]');

        $resolver->setAllowedTypes('operator_selectable', 'bool');
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_filter_data';
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (!$viewData instanceof FilterData) {
            return;
        }

        $forms = iterator_to_array($forms);
        $forms['value']->setData($viewData->getValue());
        $forms['operator']->setData($viewData->getOperator());
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

        $operator = $forms['operator']->getData();

        if (is_string($operator)) {
            $operator = Operator::tryFrom($operator);
        }

        if (null === $operator) {
            $operator = $forms['operator']->getParent()->getConfig()->getOption('default_operator');
        }

        $viewData->setValue($forms['value']->getData() ?? '');
        $viewData->setOperator($operator);
    }
}

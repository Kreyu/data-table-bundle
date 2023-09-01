<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
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
        $builder
            ->add('value', $options['form_type'], $options['form_options'])
            ->add('operator', $options['operator_form_type'], $options['operator_form_options'])
            ->setDataMapper($this)
        ;
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
                'default_operator' => Operator::Equals,
                'supported_operators' => [],
                'operator_selectable' => false,
            ])
            ->setAllowedTypes('form_type', 'string')
            ->setAllowedTypes('form_options', 'array')
            ->setAllowedTypes('operator_form_type', 'string')
            ->setAllowedTypes('operator_form_options', 'array')
            ->setAllowedTypes('operator_selectable', 'bool')
            ->setAllowedTypes('default_operator', Operator::class)
            ->setAllowedTypes('supported_operators', Operator::class.'[]')
        ;
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
        $forms['value']->setData($viewData->hasValue() ? $viewData->getValue() : null);
        $forms['operator']->setData($viewData->getOperator());
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        if (!$viewData instanceof FilterData) {
            $viewData = new FilterData();
        }

        $forms = iterator_to_array($forms);

        $operator = $forms['operator']->getData();

        if (is_string($operator)) {
            $operator = Operator::tryFrom($operator);
        }

        // TODO: Remove once the deprecated operators are removed.
        $operator = $operator?->getNonDeprecatedCase();

        $viewData->setValue($forms['value']->getData() ?? '');
        $viewData->setOperator($operator);
    }
}

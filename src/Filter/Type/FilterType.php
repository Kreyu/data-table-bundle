<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Util\StringUtil;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

final class FilterType implements FilterTypeInterface
{
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
    }

    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $setters = [
            'value_form_type' => $builder->setValueFormType(...),
            'value_form_options' => $builder->setValueFormOptions(...),
            'operator_form_type' => $builder->setOperatorFormType(...),
            'operator_form_options' => $builder->setOperatorFormOptions(...),
            'default_operator' => $builder->setDefaultOperator(...),
            'supported_operators' => $builder->setSupportedOperators(...),
            'operator_selectable' => $builder->setOperatorSelectable(...),
        ];

        foreach ($setters as $option => $setter) {
            $setter($options[$option]);
        }
    }

    public function buildView(FilterView $view, FilterInterface $filter, FilterData $data, array $options): void
    {
        $value = $data;

        if ($value->hasValue() && $formatter = $options['active_filter_formatter']) {
            $value = $formatter($data, $filter, $options);
        }

        $view->data = $data;
        $view->value = $value;

        $view->vars = array_replace($view->vars, [
            'name' => $options['name'] ?? $filter->getName(),
            'form_name' => $options['form_name'] ?? $filter->getFormName(),
            'label' => $options['label'] ?? StringUtil::camelToSentence($filter->getName()),
            'label_translation_parameters' => $options['label_translation_parameters'],
            'translation_domain' => $options['translation_domain'] ?? $view->parent->vars['translation_domain'],
            'query_path' => $options['query_path'] ?? $filter->getName(),
            'field_Type' => $options['field_type'],
            'field_options' => $options['field_options'],
            'operator_type' => $options['operator_type'],
            'operator_options' => $options['operator_options'],
            'active_filter_formatter' => $options['active_filter_formatter'],
            'data' => $view->data,
            'value' => $view->value,
            'operator_selectable' => $filter->getConfig()->isOperatorSelectable(),
            'clear_filter_parameters' => [
                'value' => $options['empty_data'],
                'operator' => $filter->getConfig()->getDefaultOperator()->value,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'label_translation_parameters' => [],
                'translation_domain' => null,
                'query_path' => null,
                'field_type' => TextType::class,
                'field_options' => [],
                'operator_type' => OperatorType::class,
                'operator_options' => [
                    'visible' => false,
                    'choices' => [],
                ],

                'value_form_type' => TextType::class,
                'value_form_options' => [],
                'operator_form_type' => OperatorType::class,
                'operator_form_options' => [],
                'default_operator' => Operator::Equal,
                'supported_operators' => [],
                'operator_selectable' => false,
                'active_filter_formatter' => function (FilterData $data): mixed {
                    return $data->getValue();
                },
                'empty_data' => '',
            ])
            ->setNormalizer('value_form_options', function (OptionsResolver $resolver, array $options): array {
                return $options + ['required' => false];
            })
            ->setAllowedTypes('label', ['null', 'bool', 'string', TranslatableMessage::class])
            ->setAllowedTypes('query_path', ['null', 'string'])
            ->setAllowedTypes('field_type', ['string'])
            ->setAllowedTypes('field_options', ['array'])
            ->setAllowedTypes('operator_type', ['string'])
            ->setAllowedTypes('operator_options', ['array'])
            ->setAllowedTypes('active_filter_formatter', ['null', 'callable'])
            ->setAllowedTypes('empty_data', ['string', 'array'])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'filter';
    }

    public function getParent(): ?string
    {
        return null;
    }
}

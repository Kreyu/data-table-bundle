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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;

final class FilterType implements FilterTypeInterface
{
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
    }

    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $setters = [
            'form_type' => $builder->setFormType(...),
            'form_options' => $builder->setFormOptions(...),
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
                'form_type' => TextType::class,
                'form_options' => [],
                'operator_form_type' => OperatorType::class,
                'operator_form_options' => [],
                'default_operator' => Operator::Equals,
                'supported_operators' => [],
                'operator_selectable' => false,
                'active_filter_formatter' => function (FilterData $data): mixed {
                    return $data->getValue();
                },
                'empty_data' => '',

                // TODO: Remove deprecated options
                'auto_alias_resolving' => true,
                'field_type' => null,
                'field_options' => [],
                'operator_type' => null,
                'operator_options' => [
                    'visible' => null,
                    'choices' => [],
                ],
            ])
            ->addNormalizer('form_options', function (Options $options, array $value): array {
                return $value + ['required' => false];
            })
            ->addNormalizer('operator_form_type', function (Options $options, string $value): string {
                return $options['operator_selectable'] ? $value : HiddenType::class;
            })
            ->addNormalizer('operator_form_options', function (Options $options, array $value): array {
                if (!$options['operator_selectable']) {
                    $value['data'] ??= $options['default_operator']->value;
                }

                if (is_a($options['operator_form_type'], OperatorType::class, true)) {
                    $value['choices'] ??= $options['supported_operators'];
                    $value['empty_data'] ??= $options['default_operator'];
                }

                return $value;
            })
            ->setAllowedTypes('label', ['null', 'bool', 'string', TranslatableInterface::class])
            ->setAllowedTypes('query_path', ['null', 'string'])
            ->setAllowedTypes('form_type', ['string'])
            ->setAllowedTypes('form_options', ['array'])
            ->setAllowedTypes('operator_form_type', ['string'])
            ->setAllowedTypes('operator_form_options', ['array'])
            ->setAllowedTypes('active_filter_formatter', ['null', 'callable'])
            ->setAllowedTypes('empty_data', ['string', 'array'])
        ;

        // TODO: Remove logic below, as it is associated with deprecated options (for backwards compatibility)
        $resolver
            ->setAllowedTypes('field_type', ['null', 'string'])
            ->setAllowedTypes('field_options', ['array'])
            ->setAllowedTypes('operator_type', ['null', 'string'])
            ->setAllowedTypes('operator_options', ['array'])
            ->setDeprecated('field_type', 'kreyu/data-table-bundle', '0.14', 'The "%name%" option is deprecated, use "form_type" instead.')
            ->setDeprecated('field_options', 'kreyu/data-table-bundle', '0.14', 'The "%name%" option is deprecated, use "form_options" instead.')
            ->setDeprecated('operator_type', 'kreyu/data-table-bundle', '0.14', 'The "%name%" option is deprecated, use "operator_form_type" instead.')
            ->setDeprecated('operator_options', 'kreyu/data-table-bundle', '0.14', 'The "%name%" option is deprecated, use "operator_form_options", "supported_operators", "operator_selectable" and "default_operator" instead.')
            ->addNormalizer('form_type', function (Options $options, mixed $value) {
                return $options['field_type'] ?? $value;
            })
            ->addNormalizer('form_options', function (Options $options, mixed $value) {
                return $options['field_options'] ?: $value;
            })
            ->addNormalizer('operator_form_type', function (Options $options, mixed $value) {
                return $options['operator_type'] ?? $value;
            })
            ->addNormalizer('operator_form_options', function (Options $options, mixed $value) {
                if ($deprecatedValue = $options['operator_options']) {
                    unset($deprecatedValue['visible'], $deprecatedValue['choices']);
                }

                return $deprecatedValue ?: $value;
            })
            ->addNormalizer('supported_operators', function (Options $options, mixed $value) {
                return ($options['operator_options']['choices'] ?? []) ?: $value;
            })
            ->addNormalizer('default_operator', function (Options $options, mixed $value) {
                $deprecatedChoices = $options['operator_options']['choices'] ?? [];

                return reset($deprecatedChoices) ?: $value;
            })
            ->addNormalizer('operator_selectable', function (Options $options, mixed $value) {
                return ($options['operator_options']['visible'] ?? null) ?: $value;
            })
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

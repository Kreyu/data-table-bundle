<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Util\StringUtil;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;

final class FilterType implements FilterTypeInterface
{
    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        /** @var class-string<FormTypeInterface> $formType */
        $formType = $options['form_type'];

        /** @var array<string, mixed> $formOptions */
        $formOptions = $options['form_options'];

        /** @var class-string<FormTypeInterface> $operatorFormType */
        $operatorFormType = $options['operator_form_type'];

        /** @var array<string, mixed> $operatorFormOptions */
        $operatorFormOptions = $options['operator_form_options'];

        /** @var Operator $defaultOperator */
        $defaultOperator = $options['default_operator'];

        /** @var array<Operator> $supportedOperators */
        $supportedOperators = $options['supported_operators'];

        /** @var bool $operatorSelectable */
        $operatorSelectable = $options['operator_selectable'];

        $builder
            ->setFormType($formType)
            ->setFormOptions($formOptions)
            ->setOperatorFormType($operatorFormType)
            ->setOperatorFormOptions($operatorFormOptions)
            ->setDefaultOperator($defaultOperator)
            ->setSupportedOperators($supportedOperators)
            ->setOperatorSelectable($operatorSelectable)
        ;
    }

    public function buildView(FilterView $view, FilterInterface $filter, FilterData $data, array $options): void
    {
        $view->data = $data;
        $view->value = $this->getActiveFilterValue($data, $filter, $options);

        $view->vars = array_replace($view->vars, [
            'name' => $filter->getName(),
            'form_name' => $filter->getFormName(),
            'query_path' => $filter->getQueryPath(),
            'label' => $options['label'] ?? StringUtil::camelToSentence($filter->getName()),
            'label_translation_parameters' => $options['label_translation_parameters'],
            'translation_domain' => $options['translation_domain'] ?? $view->parent->vars['translation_domain'] ?? null,
            'form_type' => $filter->getConfig()->getFormType(),
            'form_options' => $filter->getConfig()->getFormOptions(),
            'operator_form_type' => $filter->getConfig()->getOperatorFormType(),
            'operator_form_options' => $filter->getConfig()->getOperatorFormOptions(),
            'operator_selectable' => $filter->getConfig()->isOperatorSelectable(),
            'default_operator' => $filter->getConfig()->getDefaultOperator(),
            'supported_operators' => $filter->getConfig()->getSupportedOperators(),
            'data' => $view->data,
            'value' => $view->value,
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
                'active_filter_formatter' => null,
            ])
            ->setAllowedTypes('label', ['null', 'bool', 'string', TranslatableInterface::class])
            ->setAllowedTypes('label_translation_parameters', 'array')
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('query_path', ['null', 'string'])
            ->setAllowedTypes('form_type', 'string')
            ->setAllowedTypes('form_options', 'array')
            ->setAllowedTypes('operator_form_type', 'string')
            ->setAllowedTypes('operator_form_options', 'array')
            ->setAllowedTypes('default_operator', Operator::class)
            ->setAllowedTypes('supported_operators', Operator::class.'[]')
            ->setAllowedTypes('operator_selectable', 'bool')
            ->setAllowedTypes('active_filter_formatter', ['null', 'callable'])
            ->setAllowedValues(
                'translation_domain',
                static fn (mixed $value): bool => $value === null || $value === false || is_string($value),
            )
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

    private function getActiveFilterValue(FilterData $data, FilterInterface $filter, array $options): mixed
    {
        if (!$data->hasValue()) {
            return null;
        }

        /** @var (callable(FilterData, FilterInterface, array<array-key, mixed>): mixed)|null $formatter */
        $formatter = $options['active_filter_formatter'];

        if ($formatter === null) {
            return $data->getValue();
        }

        return $formatter($data, $filter, $options);
    }
}

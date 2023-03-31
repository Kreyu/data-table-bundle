<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
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

    public function buildView(FilterView $view, FilterInterface $filter, FilterData $data, array $options): void
    {
        $value = $data;

        if ($value->hasValue() && $formatter = $options['active_filter_formatter']) {
            $value = $formatter($data, $filter, $options);
        }

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
            'auto_alias_resolving' => $options['auto_alias_resolving'],
            'active_filter_formatter' => $options['active_filter_formatter'],
            'data' => $data,
            'value' => $value,
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
                'auto_alias_resolving' => true,
                'active_filter_formatter' => function (FilterData $data): mixed {
                    return $data->getValue();
                },
            ])
            ->setAllowedTypes('label', ['null', 'bool', 'string', TranslatableMessage::class])
            ->setAllowedTypes('query_path', ['null', 'string'])
            ->setAllowedTypes('field_type', ['string'])
            ->setAllowedTypes('field_options', ['array'])
            ->setAllowedTypes('operator_type', ['string'])
            ->setAllowedTypes('operator_options', ['array'])
            ->setAllowedTypes('auto_alias_resolving', ['bool'])
            ->setAllowedTypes('active_filter_formatter', ['null', 'callable'])
        ;
    }

    public function getParent(): ?string
    {
        return null;
    }
}

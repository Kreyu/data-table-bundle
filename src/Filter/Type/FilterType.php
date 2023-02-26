<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

final class FilterType implements FilterTypeInterface
{
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
    }

    public function buildView(FilterView $view, FilterInterface $filter, array $options): void
    {
        $resolver = clone $filter->getType()->getOptionsResolver();

        $resolver
            ->setDefaults([
                'name' => $filter->getName(),
                'label' => ucfirst($filter->getName()),
                'translation_domain' => $view->parent->vars['label_translation_domain'],
                'query_path' => $filter->getName(),
            ])
        ;

        $options = $resolver->resolve(array_filter($options));

        $view->vars = $options;
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
            ->setAllowedTypes('label', ['null', 'string', TranslatableMessage::class])
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

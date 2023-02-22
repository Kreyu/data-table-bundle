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
        $view->vars = $options;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'label_translation_parameters' => [],
                'translation_domain' => 'KreyuDataTable',
                'query_path' => null,
                'field_type' => TextType::class,
                'field_options' => [],
                'operator_type' => OperatorType::class,
                'operator_options' => [
                    'visible' => false,
                    'choices' => [],
                ],
            ])
            ->setAllowedTypes('label', ['string', TranslatableMessage::class])
            ->setAllowedTypes('query_path', ['string'])
            ->setAllowedTypes('field_type', ['string'])
            ->setAllowedTypes('field_options', ['array'])
            ->setAllowedTypes('operator_type', ['string'])
            ->setAllowedTypes('operator_options', ['array'])
        ;
    }

    public function getParent(): ?string
    {
        return null;
    }
}

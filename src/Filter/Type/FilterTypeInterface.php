<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FilterTypeInterface
{
    public function buildFilter(FilterBuilderInterface $builder, array $options): void;

    public function buildView(FilterView $view, FilterInterface $filter, FilterData $data, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    public function getBlockPrefix(): string;

    /**
     * @return class-string<FilterTypeInterface>|null
     */
    public function getParent(): ?string;
}

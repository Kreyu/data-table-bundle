<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResolvedFilterTypeInterface
{
    public function getBlockPrefix(): string;

    public function getParent(): ?ResolvedFilterTypeInterface;

    public function getInnerType(): FilterTypeInterface;

    /**
     * @return array<FilterTypeExtensionInterface>
     */
    public function getTypeExtensions(): array;

    public function createBuilder(FilterFactoryInterface $factory, string $name, array $options): FilterBuilderInterface;

    public function createView(FilterInterface $filter, FilterData $data, DataTableView $parent): FilterView;

    public function buildFilter(FilterBuilderInterface $builder, array $options): void;

    public function buildView(FilterView $view, FilterInterface $filter, FilterData $data, array $options): void;

    public function getOptionsResolver(): OptionsResolver;
}

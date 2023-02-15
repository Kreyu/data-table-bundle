<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResolvedFilterTypeInterface
{
    public function getParent(): ?ResolvedFilterTypeInterface;

    public function getInnerType(): FilterTypeInterface;

    /**
     * @return array<FilterTypeExtensionInterface>
     */
    public function getTypeExtensions(): array;

    public function createView(FilterInterface $filter, DataTableView $parent = null): FilterView;

    public function buildView(FilterView $view, FilterInterface $filter, array $options): void;

    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void;

    public function getOptionsResolver(): OptionsResolver;
}

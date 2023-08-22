<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template T of ProxyQueryInterface
 */
interface FilterTypeInterface
{
    /**
     * @param ProxyQueryInterface<T> $query
     */
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void;

    public function buildView(FilterView $view, FilterInterface $filter, FilterData $data, array $options): void;

    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * @return class-string<FilterTypeInterface>|null
     */
    public function getParent(): ?string;
}

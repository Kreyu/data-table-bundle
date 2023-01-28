<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Extension;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface FilterTypeExtensionInterface
{
    public function buildView(FilterView $view, FilterInterface $filter, array $options): void;

    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void;

    public function configureOptions(OptionsResolver $resolver): void;

    /**
     * @return iterable<class-string<FilterTypeInterface>>
     */
    public static function getExtendedTypes(): iterable;
}
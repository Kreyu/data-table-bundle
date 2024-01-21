<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomFilterTypeExtension implements FilterTypeExtensionInterface
{
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        throw new LogicException('Not implemented');
    }

    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
    }

    public function buildView(FilterView $view, FilterInterface $filter, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            CustomFilterType::class,
        ];
    }
}

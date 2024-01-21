<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\AbstractFilterType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomFilterType extends AbstractFilterType
{
    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['foo']);
    }

    public function getBlockPrefix(): string
    {
        return 'custom_filter';
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedFilterTypeDataCollectorProxy implements ResolvedFilterTypeInterface
{
    public function __construct(
        private ResolvedFilterTypeInterface $proxiedType,
        private DataTableDataCollectorInterface $dataCollector,
    ) {
    }

    public function getBlockPrefix(): string
    {
        return $this->proxiedType->getBlockPrefix();
    }

    public function getParent(): ?ResolvedFilterTypeInterface
    {
        return $this->proxiedType->getParent();
    }

    public function getInnerType(): FilterTypeInterface
    {
        return $this->proxiedType->getInnerType();
    }

    public function getTypeExtensions(): array
    {
        return $this->proxiedType->getTypeExtensions();
    }

    public function createBuilder(FilterFactoryInterface $factory, string $name, array $options): FilterBuilderInterface
    {
        $builder = $this->proxiedType->createBuilder($factory, $name, $options);
        $builder->setType($this);

        return $builder;
    }

    public function createView(FilterInterface $filter, FilterData $data, DataTableView $parent): FilterView
    {
        return $this->proxiedType->createView($filter, $data, $parent);
    }

    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $this->proxiedType->buildFilter($builder, $options);
    }

    public function buildView(FilterView $view, FilterInterface $filter, FilterData $data, array $options): void
    {
        $this->proxiedType->buildView($view, $filter, $data, $options);
        $this->dataCollector->collectFilterView($filter, $view);
    }

    public function getOptionsResolver(): OptionsResolver
    {
        return $this->proxiedType->getOptionsResolver();
    }
}

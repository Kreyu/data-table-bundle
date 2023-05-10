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

class ResolvedFilterType implements ResolvedFilterTypeInterface
{
    private OptionsResolver $optionsResolver;

    /**
     * @param array<FilterTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        private FilterTypeInterface $innerType,
        private array $typeExtensions = [],
        private ?ResolvedFilterTypeInterface $parent = null,
    ) {
    }

    public function getParent(): ?ResolvedFilterTypeInterface
    {
        return $this->parent;
    }

    public function getInnerType(): FilterTypeInterface
    {
        return $this->innerType;
    }

    public function getTypeExtensions(): array
    {
        return $this->typeExtensions;
    }

    public function createView(FilterInterface $filter, FilterData $data, DataTableView $parent): FilterView
    {
        return new FilterView($parent, $data);
    }

    public function buildView(FilterView $view, FilterInterface $filter, FilterData $data, array $options): void
    {
        $this->parent?->buildView($view, $filter, $data, $options);

        $this->innerType->buildView($view, $filter, $data, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildView($view, $filter, $options);
        }
    }

    public function apply(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter, array $options): void
    {
        $this->parent?->apply($query, $data, $filter, $options);

        $this->innerType->apply($query, $data, $filter, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->apply($query, $data, $filter, $options);
        }
    }

    public function getOptionsResolver(): OptionsResolver
    {
        if (!isset($this->optionsResolver)) {
            if (null !== $this->parent) {
                $this->optionsResolver = clone $this->parent->getOptionsResolver();
            } else {
                $this->optionsResolver = new OptionsResolver();
            }

            $this->innerType->configureOptions($this->optionsResolver);

            foreach ($this->typeExtensions as $extension) {
                $extension->configureOptions($this->optionsResolver);
            }
        }

        return $this->optionsResolver;
    }
}

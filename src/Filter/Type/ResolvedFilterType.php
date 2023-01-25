<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedFilterType implements ResolvedFilterTypeInterface
{
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

    public function createView(FilterInterface $filter, DataTableView $parent = null): FilterView
    {
        return new FilterView($parent);
    }

    public function buildView(FilterView $view, FilterInterface $filter, array $options): void
    {
        $this->parent?->buildView($view, $filter, $options);

        $this->innerType->buildView($view, $filter, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildView($view, $filter, $options);
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
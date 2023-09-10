<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilder;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
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

    public function getBlockPrefix(): string
    {
        return $this->innerType->getBlockPrefix();
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

    /**
     * @throws ExceptionInterface
     */
    public function createBuilder(FilterFactoryInterface $factory, string $name, array $options): FilterBuilderInterface
    {
        try {
            $options = $this->getOptionsResolver()->resolve($options);
        } catch (ExceptionInterface $exception) {
            throw new $exception(sprintf('An error has occurred resolving the options of the filter "%s": ', get_debug_type($this->getInnerType())).$exception->getMessage(), $exception->getCode(), $exception);
        }

        return new FilterBuilder($name, $this, $options);
    }

    public function createView(FilterInterface $filter, FilterData $data, DataTableView $parent): FilterView
    {
        return new FilterView($parent, $data);
    }

    public function buildFilter(FilterBuilderInterface $builder, array $options): void
    {
        $this->parent?->buildFilter($builder, $options);

        $this->innerType->buildFilter($builder, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildFilter($builder, $options);
        }
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

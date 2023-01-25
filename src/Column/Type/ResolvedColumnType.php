<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ResolvedColumnType implements ResolvedColumnTypeInterface
{
    /**
     * @param array<ColumnTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        private ColumnTypeInterface $innerType,
        private array $typeExtensions = [],
        private ?ResolvedColumnTypeInterface $parent = null,
    ) {
    }

    public function getBlockPrefix(): string
    {
        return $this->innerType->getBlockPrefix();
    }

    public function getParent(): ?ResolvedColumnTypeInterface
    {
        return $this->parent;
    }

    public function getInnerType(): ColumnTypeInterface
    {
        return $this->innerType;
    }

    public function getTypeExtensions(): array
    {
        return $this->typeExtensions;
    }

    public function createView(ColumnInterface $column, DataTableView $parent = null): ColumnView
    {
        return new ColumnView($parent);
    }

    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
        $this->parent?->buildView($view, $column, $options);

        $this->innerType->buildView($view, $column, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildView($view, $column, $options);
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
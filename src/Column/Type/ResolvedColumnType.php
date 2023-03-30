<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedColumnType implements ResolvedColumnTypeInterface
{
    private OptionsResolver $optionsResolver;

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

    public function createHeaderView(ColumnInterface $column, HeaderRowView $parent = null): ColumnHeaderView
    {
        return new ColumnHeaderView($parent);
    }

    public function createValueView(ColumnInterface $column, ValueRowView $parent = null): ColumnValueView
    {
        return new ColumnValueView($parent);
    }

    public function buildHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        $this->parent?->buildHeaderView($view, $column, $options);

        $this->innerType->buildHeaderView($view, $column, $options);
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $this->parent?->buildValueView($view, $column, $options);

        $this->innerType->buildValueView($view, $column, $options);
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

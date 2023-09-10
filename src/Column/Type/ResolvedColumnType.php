<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilder;
use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedColumnType implements ResolvedColumnTypeInterface
{
    private OptionsResolver $optionsResolver;

    /**
     * @param array<ColumnTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        private readonly ColumnTypeInterface $innerType,
        private readonly array $typeExtensions = [],
        private readonly ?ResolvedColumnTypeInterface $parent = null,
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

    /**
     * @throws ExceptionInterface
     */
    public function createBuilder(ColumnFactoryInterface $factory, string $name, array $options): ColumnBuilderInterface
    {
        try {
            $options = $this->getOptionsResolver()->resolve($options);
        } catch (ExceptionInterface $exception) {
            throw new $exception(sprintf('An error has occurred resolving the options of the column "%s": ', get_debug_type($this->getInnerType())).$exception->getMessage(), $exception->getCode(), $exception);
        }

        $builder = new ColumnBuilder($name, $this, $options);
        $builder->setColumnFactory($factory);

        return $builder;
    }

    public function createHeaderView(ColumnInterface $column, HeaderRowView $parent = null): ColumnHeaderView
    {
        return new ColumnHeaderView($parent);
    }

    public function createValueView(ColumnInterface $column, ValueRowView $parent = null): ColumnValueView
    {
        return new ColumnValueView($parent);
    }

    public function createExportHeaderView(ColumnInterface $column, HeaderRowView $parent = null): ColumnHeaderView
    {
        return new ColumnHeaderView($parent);
    }

    public function createExportValueView(ColumnInterface $column, ValueRowView $parent = null): ColumnValueView
    {
        return new ColumnValueView($parent);
    }

    public function buildColumn(ColumnBuilderInterface $builder, array $options): void
    {
        $this->parent?->buildColumn($builder, $options);

        $this->innerType->buildColumn($builder, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildColumn($builder, $options);
        }
    }

    public function buildHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        $this->parent?->buildHeaderView($view, $column, $options);

        $this->innerType->buildHeaderView($view, $column, $options);

        foreach ($this->typeExtensions as $typeExtension) {
            $typeExtension->buildHeaderView($view, $column, $options);
        }
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $this->parent?->buildValueView($view, $column, $options);

        $this->innerType->buildValueView($view, $column, $options);

        foreach ($this->typeExtensions as $typeExtension) {
            $typeExtension->buildValueView($view, $column, $options);
        }
    }

    public function buildExportHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        $this->parent?->buildExportHeaderView($view, $column, $options);

        $this->innerType->buildExportHeaderView($view, $column, $options);

        foreach ($this->typeExtensions as $typeExtension) {
            $typeExtension->buildExportHeaderView($view, $column, $options);
        }
    }

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $this->parent?->buildExportValueView($view, $column, $options);

        $this->innerType->buildExportValueView($view, $column, $options);

        foreach ($this->typeExtensions as $typeExtension) {
            $typeExtension->buildExportValueView($view, $column, $options);
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

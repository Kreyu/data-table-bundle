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
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedColumnType implements ResolvedColumnTypeInterface
{
    private ?OptionsResolver $optionsResolver = null;

    /**
     * @param array<ColumnTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        private ColumnTypeInterface $innerType,
        private array $typeExtensions = [],
        private ?ResolvedColumnTypeInterface $parent = null,
    ) {}

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
            throw new $exception(
                sprintf(
                    'An error has occurred resolving the options of the column "%s": %s',
                    get_debug_type($this->getInnerType()),
                    $exception->getMessage(),
                ),
                $exception->getCode(),
                $exception,
            );
        }

        $builder = new ColumnBuilder($name, $this, $options);
        $builder->setColumnFactory($factory);

        return $builder;
    }

    public function createHeaderView(ColumnInterface $column, ?HeaderRowView $parent = null): ColumnHeaderView
    {
        return $this->createHeaderViewWithParent($parent);
    }

    public function createValueView(ColumnInterface $column, ?ValueRowView $parent = null): ColumnValueView
    {
        return $this->createValueViewWithParent($parent);
    }

    public function createExportHeaderView(ColumnInterface $column, ?HeaderRowView $parent = null): ColumnHeaderView
    {
        return $this->createHeaderViewWithParent($parent);
    }

    public function createExportValueView(ColumnInterface $column, ?ValueRowView $parent = null): ColumnValueView
    {
        return $this->createValueViewWithParent($parent);
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
        if ($this->optionsResolver !== null) {
            return $this->optionsResolver;
        }

        $parentOptionsResolver = $this->parent?->getOptionsResolver();
        $optionsResolver = $parentOptionsResolver === null ? new OptionsResolver() : clone $parentOptionsResolver;

        $this->innerType->configureOptions($optionsResolver);

        foreach ($this->typeExtensions as $extension) {
            $extension->configureOptions($optionsResolver);
        }

        $this->optionsResolver = $optionsResolver;

        return $optionsResolver;
    }

    private function createHeaderViewWithParent(?HeaderRowView $parent): ColumnHeaderView
    {
        if ($parent === null) {
            throw new InvalidArgumentException('Creating a column header view requires a parent header row view.');
        }

        return new ColumnHeaderView($parent);
    }

    private function createValueViewWithParent(?ValueRowView $parent): ColumnValueView
    {
        if ($parent === null) {
            throw new InvalidArgumentException('Creating a column value view requires a parent value row view.');
        }

        return new ColumnValueView($parent);
    }
}

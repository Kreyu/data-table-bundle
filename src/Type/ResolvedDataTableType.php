<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\DataTableBuilder;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedDataTableType implements ResolvedDataTableTypeInterface
{
    private ?OptionsResolver $optionsResolver = null;

    /**
     * @param array<DataTableTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        private readonly DataTableTypeInterface $innerType,
        private readonly array $typeExtensions = [],
        private readonly ?ResolvedDataTableTypeInterface $parent = null,
    ) {}

    public function getName(): string
    {
        return $this->innerType->getName();
    }

    public function getInnerType(): DataTableTypeInterface
    {
        return $this->innerType;
    }

    public function getParent(): ?ResolvedDataTableTypeInterface
    {
        return $this->parent;
    }

    public function getTypeExtensions(): array
    {
        return $this->typeExtensions;
    }

    /**
     * @throws ExceptionInterface
     */
    public function createBuilder(
        DataTableFactoryInterface $factory,
        string $name,
        ?ProxyQueryInterface $query = null,
        array $options = [],
    ): DataTableBuilderInterface {
        try {
            /** @var array<string, mixed> $resolvedOptions */
            $resolvedOptions = $this->getOptionsResolver()->resolve($options);
        } catch (ExceptionInterface $exception) {
            throw new $exception(
                sprintf(
                    'An error has occurred resolving the options of the data table "%s": %s',
                    get_debug_type($this->getInnerType()),
                    $exception->getMessage(),
                ),
                $exception->getCode(),
                $exception,
            );
        }

        return new DataTableBuilder($name, $this, $query, new EventDispatcher(), $resolvedOptions);
    }

    public function createDefaultQuery(array $options): mixed
    {
        return $this->innerType->createDefaultQuery($options) ?? $this->parent?->createDefaultQuery($options);
    }

    public function createView(DataTableInterface $dataTable): DataTableView
    {
        return new DataTableView();
    }

    public function createExportView(DataTableInterface $dataTable): DataTableView
    {
        return new DataTableView();
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $this->parent?->buildDataTable($builder, $options);

        $this->innerType->buildDataTable($builder, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildDataTable($builder, $options);
        }
    }

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
        $this->parent?->buildView($view, $dataTable, $options);

        $this->innerType->buildView($view, $dataTable, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildView($view, $dataTable, $options);
        }
    }

    public function buildExportView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
        if (null !== $this->parent) {
            $this->parent->buildExportView($view, $dataTable, $options);
        }

        $this->innerType->buildExportView($view, $dataTable, $options);
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
}

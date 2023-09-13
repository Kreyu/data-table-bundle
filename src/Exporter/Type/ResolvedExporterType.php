<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilder;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterTypeExtensionInterface;
use Symfony\Component\OptionsResolver\Exception\ExceptionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResolvedExporterType implements ResolvedExporterTypeInterface
{
    private OptionsResolver $optionsResolver;

    /**
     * @param array<ExporterTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        private readonly ExporterTypeInterface $innerType,
        private readonly array $typeExtensions = [],
        private readonly ?ResolvedExporterTypeInterface $parent = null,
    ) {
    }

    public function getName(): string
    {
        return $this->innerType->getName();
    }

    public function getParent(): ?ResolvedExporterTypeInterface
    {
        return $this->parent;
    }

    public function getInnerType(): ExporterTypeInterface
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
    public function createBuilder(ExporterFactoryInterface $factory, string $name, array $options): ExporterBuilderInterface
    {
        try {
            $options = $this->getOptionsResolver()->resolve($options);
        } catch (ExceptionInterface $exception) {
            throw new $exception(sprintf('An error has occurred resolving the options of the exporter "%s": ', get_debug_type($this->getInnerType())).$exception->getMessage(), $exception->getCode(), $exception);
        }

        return new ExporterBuilder($name, $this, $options);
    }

    public function buildExporter(ExporterBuilderInterface $builder, array $options): void
    {
        $this->parent?->buildExporter($builder, $options);

        $this->innerType->buildExporter($builder, $options);

        foreach ($this->typeExtensions as $extension) {
            $extension->buildExporter($builder, $options);
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

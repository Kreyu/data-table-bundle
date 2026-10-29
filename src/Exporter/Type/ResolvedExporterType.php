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
    private ?OptionsResolver $optionsResolver = null;

    /**
     * @param array<ExporterTypeExtensionInterface> $typeExtensions
     */
    public function __construct(
        private readonly ExporterTypeInterface $innerType,
        private readonly array $typeExtensions = [],
        private readonly ?ResolvedExporterTypeInterface $parent = null,
    ) {}

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
    public function createBuilder(
        ExporterFactoryInterface $factory,
        string $name,
        array $options,
    ): ExporterBuilderInterface {
        try {
            /** @var array<string, mixed> $resolvedOptions */
            $resolvedOptions = $this->getOptionsResolver()->resolve($options);
        } catch (ExceptionInterface $exception) {
            throw new $exception(
                sprintf(
                    'An error has occurred resolving the options of the exporter "%s": %s',
                    get_debug_type($this->getInnerType()),
                    $exception->getMessage(),
                ),
                $exception->getCode(),
                $exception,
            );
        }

        return new ExporterBuilder($name, $this, $resolvedOptions);
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

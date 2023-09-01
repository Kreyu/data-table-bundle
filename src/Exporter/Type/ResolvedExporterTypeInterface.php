<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterTypeExtensionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

interface ResolvedExporterTypeInterface
{
    public function getName(): string;

    public function getParent(): ?ResolvedExporterTypeInterface;

    public function getInnerType(): ExporterTypeInterface;

    /**
     * @return array<ExporterTypeExtensionInterface>
     */
    public function getTypeExtensions(): array;

    public function createBuilder(ExporterFactoryInterface $factory, string $name, array $options): ExporterBuilderInterface;

    public function buildExporter(ExporterBuilderInterface $builder, array $options): void;

    public function getOptionsResolver(): OptionsResolver;
}

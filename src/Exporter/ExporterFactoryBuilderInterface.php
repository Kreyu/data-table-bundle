<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactoryInterface;

interface ExporterFactoryBuilderInterface
{
    public function setResolvedTypeFactory(ResolvedExporterTypeFactoryInterface $resolvedTypeFactory): static;

    public function addExtension(ExporterExtensionInterface $extension): static;

    /**
     * @param array<ExporterExtensionInterface> $extensions
     */
    public function addExtensions(array $extensions): static;

    public function addType(ExporterTypeInterface $type): static;

    /**
     * @param array<ExporterTypeInterface> $types
     */
    public function addTypes(array $types): static;

    public function addTypeExtension(ExporterTypeExtensionInterface $typeExtension): static;

    /**
     * @param array<ExporterTypeExtensionInterface> $typeExtensions
     */
    public function addTypeExtensions(array $typeExtensions): static;

    public function getExporterFactory(): ExporterFactoryInterface;
}

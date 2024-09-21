<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

interface ExporterConfigBuilderInterface extends ExporterConfigInterface
{
    /**
     * @deprecated since 0.14.0, provide the name using the factory {@see ExporterFactoryInterface} "named" methods instead
     */
    public function setName(string $name): static;

    public function setType(ResolvedExporterTypeInterface $type): static;

    /**
     * @deprecated since 0.14.0, modifying the options dynamically will be removed as it creates unexpected behaviors
     */
    public function setOptions(array $options): static;

    /**
     * @deprecated since 0.14.0, modifying the options dynamically will be removed as it creates unexpected behaviors
     */
    public function setOption(string $name, mixed $value): static;

    public function setAttribute(string $name, mixed $value): static;

    public function setAttributes(array $attributes): static;

    public function getExporterConfig(): ExporterConfigInterface;
}

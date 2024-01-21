<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Extension;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;

class PreloadedExporterExtension extends AbstractExporterExtension
{
    /**
     * @param array<ExporterTypeInterface> $types
     * @param array<ExporterTypeExtensionInterface>|array<string, array<ExporterTypeExtensionInterface>> $typeExtensions
     */
    public function __construct(
        private readonly array $types = [],
        private readonly array $typeExtensions = [],
    ) {
    }

    protected function loadTypes(): array
    {
        return $this->types;
    }

    protected function loadTypeExtensions(): array
    {
        return $this->typeExtensions;
    }
}

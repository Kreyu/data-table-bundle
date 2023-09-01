<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterTypeExtensionInterface;

interface ResolvedExporterTypeFactoryInterface
{
    /**
     * @param array<ExporterTypeExtensionInterface> $typeExtensions
     */
    public function createResolvedType(ExporterTypeInterface $type, array $typeExtensions = [], ResolvedExporterTypeInterface $parent = null): ResolvedExporterTypeInterface;
}

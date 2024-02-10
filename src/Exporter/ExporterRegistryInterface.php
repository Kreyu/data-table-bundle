<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

interface ExporterRegistryInterface
{
    /**
     * @param class-string<ExporterTypeInterface> $name
     */
    public function getType(string $name): ResolvedExporterTypeInterface;

    /**
     * @param class-string<ExporterTypeInterface> $name
     */
    public function hasType(string $name): bool;
}

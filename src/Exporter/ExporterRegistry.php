<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\AbstractRegistry;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

/**
 * @extends AbstractRegistry<ExporterTypeInterface, ResolvedExporterTypeInterface, ExporterExtensionInterface>
 */
class ExporterRegistry extends AbstractRegistry implements ExporterRegistryInterface
{
    public function getType(string $name): ResolvedExporterTypeInterface
    {
        return $this->doGetType($name);
    }

    final protected function getErrorContextName(): string
    {
        return 'exporter';
    }

    final protected function getTypeClass(): string
    {
        return ExporterTypeInterface::class;
    }

    final protected function getExtensionClass(): string
    {
        return ExporterExtensionInterface::class;
    }
}

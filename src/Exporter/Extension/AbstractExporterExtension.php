<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Extension;

use Kreyu\Bundle\DataTableBundle\AbstractExtension;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;

/**
 * @extends AbstractExtension<ExporterTypeInterface, ExporterTypeExtensionInterface>
 */
abstract class AbstractExporterExtension extends AbstractExtension implements ExporterExtensionInterface
{
    public function getType(string $name): ExporterTypeInterface
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

    final protected function getTypeExtensionClass(): string
    {
        return ExporterTypeExtensionInterface::class;
    }
}
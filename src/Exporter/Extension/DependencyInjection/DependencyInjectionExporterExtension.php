<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Extension\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\AbstractDependencyInjectionExtension;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;

class DependencyInjectionExporterExtension extends AbstractDependencyInjectionExtension implements ExporterExtensionInterface
{
    public function getType(string $name): ExporterTypeInterface
    {
        return $this->doGetType($name);
    }

    protected function getTypeClass(): string
    {
        return ExporterTypeInterface::class;
    }

    protected function getErrorContextName(): string
    {
        return 'filter';
    }
}

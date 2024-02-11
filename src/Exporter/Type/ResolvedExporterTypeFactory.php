<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

class ResolvedExporterTypeFactory implements ResolvedExporterTypeFactoryInterface
{
    public function createResolvedType(ExporterTypeInterface $type, array $typeExtensions = [], ?ResolvedExporterTypeInterface $parent = null): ResolvedExporterTypeInterface
    {
        return new ResolvedExporterType($type, $typeExtensions, $parent);
    }
}

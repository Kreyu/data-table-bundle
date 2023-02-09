<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter\Type;

interface ResolvedExporterTypeFactoryInterface
{
    public function createResolvedType(ExporterTypeInterface $type, ResolvedExporterTypeInterface $parent = null): ResolvedExporterTypeInterface;
}

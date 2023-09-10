<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

interface ExporterBuilderInterface extends ExporterConfigBuilderInterface
{
    public function getExporter(): ExporterInterface;
}

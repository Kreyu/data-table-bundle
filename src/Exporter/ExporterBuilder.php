<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

class ExporterBuilder extends ExporterConfigBuilder implements ExporterBuilderInterface
{
    public function getExporter(): ExporterInterface
    {
        return new Exporter($this->getExporterConfig());
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exporter;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;

class ExporterBuilder extends ExporterConfigBuilder implements ExporterBuilderInterface
{
    public function getExporter(): ExporterInterface
    {
        if ($this->locked) {
            throw $this->createBuilderLockedException();
        }

        return new Exporter($this->getExporterConfig());
    }

    private function createBuilderLockedException(): BadMethodCallException
    {
        return new BadMethodCallException('FilterBuilder methods cannot be accessed anymore once the builder is turned into a FilterConfigInterface instance.');
    }
}

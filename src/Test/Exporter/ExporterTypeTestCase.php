<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;

abstract class ExporterTypeTestCase extends ExporterIntegrationTestCase
{
    /**
     * @return class-string<ExporterTypeInterface>
     */
    abstract protected function getTestedType(): string;

    protected function createExporter(array $options = []): ExporterInterface
    {
        return $this->factory->create($this->getTestedType(), $options);
    }

    protected function createNamedExporter(string $name, array $options = []): ExporterInterface
    {
        return $this->factory->createNamed($name, $this->getTestedType(), $options);
    }
}

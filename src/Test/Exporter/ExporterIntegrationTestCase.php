<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactory;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterRegistry;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactory;
use PHPUnit\Framework\TestCase;

abstract class ExporterIntegrationTestCase extends TestCase
{
    protected ExporterRegistry $registry;
    protected ExporterFactory $factory;

    protected function setUp(): void
    {
        $this->registry = new ExporterRegistry(
            types: $this->getTypes(),
            typeExtensions: $this->getTypeExtensions(),
            resolvedTypeFactory: new ResolvedExporterTypeFactory(),
        );

        $this->factory = new ExporterFactory($this->registry);
    }

    protected function getExtensions(): array
    {
        return [];
    }

    protected function getTypeExtensions(): array
    {
        return [];
    }

    protected function getTypes(): array
    {
        return [];
    }
}

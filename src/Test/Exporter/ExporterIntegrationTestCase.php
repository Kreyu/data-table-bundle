<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Exporter;

use Kreyu\Bundle\DataTableBundle\DataTables;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use PHPUnit\Framework\TestCase;

abstract class ExporterIntegrationTestCase extends TestCase
{
    protected ExporterFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = DataTables::createExporterFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions($this->getTypeExtensions())
            ->addTypes($this->getTypes())
            ->getExporterFactory();
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

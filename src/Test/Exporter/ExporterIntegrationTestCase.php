<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Exporter;

use Kreyu\Bundle\DataTableBundle\DataTables;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
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
            ->getExporterFactory()
        ;
    }

    /**
     * @return array<ExporterExtensionInterface>
     */
    protected function getExtensions(): array
    {
        return [];
    }

    /**
     * @return array<ExporterTypeExtensionInterface>
     */
    protected function getTypeExtensions(): array
    {
        return [];
    }

    /**
     * @return array<ExporterTypeInterface>
     */
    protected function getTypes(): array
    {
        return [];
    }
}

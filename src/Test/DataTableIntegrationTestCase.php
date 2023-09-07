<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test;

use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\DataTables;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use PHPUnit\Framework\TestCase;

abstract class DataTableIntegrationTestCase extends TestCase
{
    protected DataTableFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = DataTables::createDataTableFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions($this->getTypeExtensions())
            ->addTypes($this->getTypes())
            ->getDataTableFactory()
        ;
    }

    /**
     * @return array<DataTableExtensionInterface>
     */
    protected function getExtensions(): array
    {
        return [];
    }

    /**
     * @return array<DataTableTypeExtensionInterface>
     */
    protected function getTypeExtensions(): array
    {
        return [];
    }

    /**
     * @return array<DataTableTypeInterface>
     */
    protected function getTypes(): array
    {
        return [];
    }
}

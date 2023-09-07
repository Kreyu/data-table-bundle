<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTables;
use PHPUnit\Framework\TestCase;

class ColumnIntegrationTestCase extends TestCase
{
    protected ColumnFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = DataTables::createColumnFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions($this->getTypeExtensions())
            ->addTypes($this->getTypes())
            ->getColumnFactory()
        ;
    }

    /**
     * @return array<ColumnExtensionInterface>
     */
    protected function getExtensions(): array
    {
        return [];
    }

    /**
     * @return array<ColumnTypeExtensionInterface>
     */
    protected function getTypeExtensions(): array
    {
        return [];
    }

    /**
     * @return array<ColumnTypeInterface>
     */
    protected function getTypes(): array
    {
        return [];
    }
}

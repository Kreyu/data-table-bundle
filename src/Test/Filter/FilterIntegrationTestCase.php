<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Filter;

use Kreyu\Bundle\DataTableBundle\DataTables;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use PHPUnit\Framework\TestCase;

abstract class FilterIntegrationTestCase extends TestCase
{
    protected FilterFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = DataTables::createFilterFactoryBuilder()
            ->addExtensions($this->getExtensions())
            ->addTypeExtensions($this->getTypeExtensions())
            ->addTypes($this->getTypes())
            ->getFilterFactory();
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

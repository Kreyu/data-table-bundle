<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Filter;

use Kreyu\Bundle\DataTableBundle\DataTables;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactory;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactory;
use PHPUnit\Framework\TestCase;

abstract class FilterIntegrationTestCase extends TestCase
{
    protected FilterRegistry $registry;
    protected FilterFactory $factory;

    protected function setUp(): void
    {
        $this->registry = new FilterRegistry(
            types: $this->getTypes(),
            typeExtensions: $this->getTypeExtensions(),
            resolvedTypeFactory: new ResolvedFilterTypeFactory(),
        );

        $this->factory = new FilterFactory($this->registry);
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

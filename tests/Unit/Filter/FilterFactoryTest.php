<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterFactory;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\ConfigurableFilterType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\SimpleFilterType;
use PHPUnit\Framework\TestCase;

class FilterFactoryTest extends TestCase
{
    public function testCreateNamedBuilder()
    {
        $builder = $this->createFactory()->createNamedBuilder('name', ConfigurableFilterType::class, options: [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('a', $builder->getOption('foo'));
        $this->assertSame('b', $builder->getOption('bar'));
    }

    public function testCreateBuilderUsesFilterName()
    {
        $builder = $this->createFactory()->createBuilder(SimpleFilterType::class);

        $this->assertSame('simple', $builder->getName());
    }

    public function testCreate()
    {
        $filter = $this->createFactory()->create(ConfigurableFilterType::class, [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('configurable', $filter->getName());
        $this->assertSame('a', $filter->getConfig()->getOption('foo'));
        $this->assertSame('b', $filter->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableFilterType::class, $filter->getConfig()->getType()->getInnerType());
    }

    public function testCreateNamed()
    {
        $filter = $this->createFactory()->createNamed('name', ConfigurableFilterType::class, [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('name', $filter->getName());
        $this->assertSame('a', $filter->getConfig()->getOption('foo'));
        $this->assertSame('b', $filter->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableFilterType::class, $filter->getConfig()->getType()->getInnerType());
    }

    private function createFactory(): FilterFactory
    {
        $registry = new FilterRegistry(
            types: [
                new FilterType(),
                new SimpleFilterType(),
                new ConfigurableFilterType(),
            ],
            typeExtensions: [],
            resolvedTypeFactory: new ResolvedFilterTypeFactory(),
        );

        return new FilterFactory($registry);
    }
}
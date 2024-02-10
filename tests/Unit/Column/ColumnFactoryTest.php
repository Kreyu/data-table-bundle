<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\ConfigurableColumnType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\SimpleColumnType;
use PHPUnit\Framework\TestCase;

class ColumnFactoryTest extends TestCase
{
    public function testCreateNamedBuilder()
    {
        $builder = $this->createFactory()->createNamedBuilder('name', ConfigurableColumnType::class, options: [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('a', $builder->getOption('foo'));
        $this->assertSame('b', $builder->getOption('bar'));
    }

    public function testCreateBuilderUsesColumnName()
    {
        $builder = $this->createFactory()->createBuilder(SimpleColumnType::class);

        $this->assertSame('simple', $builder->getName());
    }

    public function testCreate()
    {
        $column = $this->createFactory()->create(ConfigurableColumnType::class, [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('configurable', $column->getName());
        $this->assertSame('a', $column->getConfig()->getOption('foo'));
        $this->assertSame('b', $column->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableColumnType::class, $column->getConfig()->getType()->getInnerType());
    }

    public function testCreateNamed()
    {
        $column = $this->createFactory()->createNamed('name', ConfigurableColumnType::class, [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('name', $column->getName());
        $this->assertSame('a', $column->getConfig()->getOption('foo'));
        $this->assertSame('b', $column->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableColumnType::class, $column->getConfig()->getType()->getInnerType());
    }

    private function createFactory(): ColumnFactory
    {
        $registry = new ColumnRegistry(
            types: [
                new ColumnType(),
                new SimpleColumnType(),
                new ConfigurableColumnType(),
            ],
            typeExtensions: [],
            resolvedTypeFactory: new ResolvedColumnTypeFactory(),
        );

        return new ColumnFactory($registry);
    }
}
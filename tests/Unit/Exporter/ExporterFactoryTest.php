<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactory;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterRegistry;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\ConfigurableExporterType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\SimpleExporterType;
use PHPUnit\Framework\TestCase;

class ExporterFactoryTest extends TestCase
{
    public function testCreateNamedBuilder()
    {
        $builder = $this->createFactory()->createNamedBuilder('name', ConfigurableExporterType::class, options: [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('a', $builder->getOption('foo'));
        $this->assertSame('b', $builder->getOption('bar'));
    }

    public function testCreateBuilderUsesExporterName()
    {
        $builder = $this->createFactory()->createBuilder(SimpleExporterType::class);

        $this->assertSame('simple', $builder->getName());
    }

    public function testCreate()
    {
        $column = $this->createFactory()->create(ConfigurableExporterType::class, [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('configurable', $column->getName());
        $this->assertSame('a', $column->getConfig()->getOption('foo'));
        $this->assertSame('b', $column->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableExporterType::class, $column->getConfig()->getType()->getInnerType());
    }

    public function testCreateNamed()
    {
        $column = $this->createFactory()->createNamed('name', ConfigurableExporterType::class, [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('name', $column->getName());
        $this->assertSame('a', $column->getConfig()->getOption('foo'));
        $this->assertSame('b', $column->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableExporterType::class, $column->getConfig()->getType()->getInnerType());
    }

    private function createFactory(): ExporterFactory
    {
        $registry = new ExporterRegistry(
            types: [
                new ExporterType(),
                new SimpleExporterType(),
                new ConfigurableExporterType(),
            ],
            typeExtensions: [],
            resolvedTypeFactory: new ResolvedExporterTypeFactory(),
        );

        return new ExporterFactory($registry);
    }
}

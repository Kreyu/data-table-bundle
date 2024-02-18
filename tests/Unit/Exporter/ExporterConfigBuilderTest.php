<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Exporter;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterConfigBuilder;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterConfigInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use PHPUnit\Framework\TestCase;

class ExporterConfigBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testGetName()
    {
        $this->assertSame('foo', $this->createBuilder()->getName());
    }

    public function testGetType()
    {
        $type = $this->createStub(ResolvedExporterTypeInterface::class);

        $this->assertSame($type, $this->createBuilder(type: $type)->getType());
    }

    public function testGetOptions()
    {
        $this->assertSame(['foo' => 'bar'], $this->createBuilder(options: ['foo' => 'bar'])->getOptions());
    }

    public function testGetOption()
    {
        $this->assertSame('bar', $this->createBuilder(options: ['foo' => 'bar'])->getOption('foo'));
    }

    public function testGetOptionDefault()
    {
        $this->assertSame('bar', $this->createBuilder()->getOption('foo', 'bar'));
    }

    public function testHasOption()
    {
        $builder = $this->createBuilder(options: ['foo' => 'bar']);

        $this->assertTrue($builder->hasOption('foo'));
        $this->assertFalse($builder->hasOption('bar'));
    }

    public function testGetExporterConfig()
    {
        $config = $this->createBuilder()->getExporterConfig();

        $this->assertInstanceOf(ExporterConfigInterface::class, $config);
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    private function createBuilder(?ResolvedExporterTypeInterface $type = null, array $options = []): ExporterConfigBuilder
    {
        return new ExporterConfigBuilder(
            name: 'foo',
            type: $type ?? $this->createStub(ResolvedExporterTypeInterface::class),
            options: $options,
        );
    }
}

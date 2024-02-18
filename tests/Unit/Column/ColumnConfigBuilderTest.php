<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnConfigBuilder;
use Kreyu\Bundle\DataTableBundle\Column\ColumnConfigInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyPath;

class ColumnConfigBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testGetName()
    {
        $this->assertSame('foo', $this->createBuilder()->getName());
    }

    public function testGetType()
    {
        $type = $this->createStub(ResolvedColumnTypeInterface::class);

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

    public function testGetAttributes()
    {
        $attributes = ['foo' => 'bar'];

        $builder = $this->createBuilder();
        $builder->setAttributes($attributes);

        $this->assertSame($attributes, $builder->getAttributes());
    }

    public function testGetAttribute()
    {
        $builder = $this->createBuilder();
        $builder->setAttribute('foo', 'bar');

        $this->assertSame('bar', $builder->getAttribute('foo'));
    }

    public function testGetAttributeDefault()
    {
        $this->assertSame('bar', $this->createBuilder()->getAttribute('foo', 'bar'));
    }

    public function testHasAttribute()
    {
        $builder = $this->createBuilder();
        $builder->setAttributes(['foo' => 'bar']);

        $this->assertTrue($builder->hasAttribute('foo'));
        $this->assertFalse($builder->hasAttribute('bar'));
    }

    public function testGetPropertyPath()
    {
        $builder = $this->createBuilder();
        $builder->setPropertyPath('foo');

        $this->assertEquals(new PropertyPath('foo'), $builder->getPropertyPath());
    }

    public function testGetSortPropertyPath()
    {
        $builder = $this->createBuilder();
        $builder->setSortPropertyPath('foo');

        $this->assertEquals(new PropertyPath('foo'), $builder->getSortPropertyPath());
    }

    public function testIsSortable()
    {
        $builder = $this->createBuilder();

        $this->assertTrue($builder->setSortable(true)->isSortable());
        $this->assertFalse($builder->setSortable(false)->isSortable());
    }

    public function testIsExportable()
    {
        $builder = $this->createBuilder();

        $this->assertTrue($builder->setExportable(true)->isExportable());
        $this->assertFalse($builder->setExportable(false)->isExportable());
    }

    public function testIsPersonalizable()
    {
        $builder = $this->createBuilder();

        $this->assertTrue($builder->setPersonalizable(true)->isPersonalizable());
        $this->assertFalse($builder->setPersonalizable(false)->isPersonalizable());
    }

    public function testGetColumnFactoryWithoutFactorySet()
    {
        $this->expectException(BadMethodCallException::class);

        $builder = $this->createBuilder();
        $builder->getColumnFactory();
    }

    public function testGetColumnFactory()
    {
        $factory = $this->createStub(ColumnFactoryInterface::class);

        $builder = $this->createBuilder();
        $builder->setColumnFactory($factory);

        $this->assertSame($factory, $builder->getColumnFactory());
    }

    public function testGetColumnConfig()
    {
        $config = $this->createBuilder()->getColumnConfig();

        $this->assertInstanceOf(ColumnConfigInterface::class, $config);
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    private function createBuilder(?ResolvedColumnTypeInterface $type = null, array $options = []): ColumnConfigBuilder
    {
        return new ColumnConfigBuilder(
            name: 'foo',
            type: $type ?? $this->createStub(ResolvedColumnTypeInterface::class),
            options: $options,
        );
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Action;

use Kreyu\Bundle\DataTableBundle\Action\ActionConfigBuilder;
use Kreyu\Bundle\DataTableBundle\Action\ActionConfigInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use PHPUnit\Framework\TestCase;

class ActionConfigBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testGetName()
    {
        $this->assertSame('foo', $this->createBuilder()->getName());
    }

    public function testGetType()
    {
        $type = $this->createStub(ResolvedActionTypeInterface::class);

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

    public function testGetContextDefault()
    {
        $this->assertSame(ActionContext::Global, $this->createBuilder()->getContext());
    }

    public function testGetContext()
    {
        $builder = $this->createBuilder();
        $builder->setContext(ActionContext::Row);

        $this->assertSame(ActionContext::Row, $builder->getContext());
    }

    public function testIsConfirmable()
    {
        $builder = $this->createBuilder();

        $this->assertTrue($builder->setConfirmable(true)->isConfirmable());
        $this->assertFalse($builder->setConfirmable(false)->isConfirmable());
    }

    public function testGetActionConfig()
    {
        $config = $this->createBuilder()->getActionConfig();

        $this->assertInstanceOf(ActionConfigInterface::class, $config);
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    private function createBuilder(?ResolvedActionTypeInterface $type = null, array $options = []): ActionConfigBuilder
    {
        return new ActionConfigBuilder(
            name: 'foo',
            type: $type ?? $this->createStub(ResolvedActionTypeInterface::class),
            options: $options,
        );
    }
}

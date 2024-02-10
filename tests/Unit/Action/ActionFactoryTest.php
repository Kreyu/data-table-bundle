<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Action;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactory;
use Kreyu\Bundle\DataTableBundle\Action\ActionRegistry;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\ConfigurableActionType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\SimpleActionType;
use PHPUnit\Framework\TestCase;

class ActionFactoryTest extends TestCase
{
    public function testCreateNamedBuilder()
    {
        $builder = $this->createFactory()->createNamedBuilder('name', ConfigurableActionType::class, options: [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('a', $builder->getOption('foo'));
        $this->assertSame('b', $builder->getOption('bar'));
    }

    public function testCreateBuilderUsesActionName()
    {
        $builder = $this->createFactory()->createBuilder(SimpleActionType::class);

        $this->assertSame('simple', $builder->getName());
    }

    public function testCreate()
    {
        $filter = $this->createFactory()->create(ConfigurableActionType::class, [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('configurable', $filter->getName());
        $this->assertSame('a', $filter->getConfig()->getOption('foo'));
        $this->assertSame('b', $filter->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableActionType::class, $filter->getConfig()->getType()->getInnerType());
    }

    public function testCreateNamed()
    {
        $filter = $this->createFactory()->createNamed('name', ConfigurableActionType::class, [
            'foo' => 'a',
            'bar' => 'b',
        ]);

        $this->assertSame('name', $filter->getName());
        $this->assertSame('a', $filter->getConfig()->getOption('foo'));
        $this->assertSame('b', $filter->getConfig()->getOption('bar'));
        $this->assertInstanceOf(ConfigurableActionType::class, $filter->getConfig()->getType()->getInnerType());
    }

    private function createFactory(): ActionFactory
    {
        $registry = new ActionRegistry(
            types: [
                new ActionType(),
                new SimpleActionType(),
                new ConfigurableActionType(),
            ],
            typeExtensions: [],
            resolvedTypeFactory: new ResolvedActionTypeFactory(),
        );

        return new ActionFactory($registry);
    }
}
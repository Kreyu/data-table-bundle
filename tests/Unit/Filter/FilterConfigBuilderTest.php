<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterConfigBuilder;
use Kreyu\Bundle\DataTableBundle\Filter\FilterConfigInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterHandlerInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\OperatorType;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\ImmutableEventDispatcher;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class FilterConfigBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testAddEventListener()
    {
        $listener = fn () => null;

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())->method('addListener')->with('foo', $listener, 100);

        $this->createBuilder(dispatcher: $dispatcher)->addEventListener('foo', $listener, 100);
    }

    public function testAddEventSubscriber()
    {
        $subscriber = $this->createStub(EventSubscriberInterface::class);

        $dispatcher = $this->createMock(EventDispatcherInterface::class);
        $dispatcher->expects($this->once())->method('addSubscriber')->with($subscriber);

        $this->createBuilder(dispatcher: $dispatcher)->addEventSubscriber($subscriber);
    }

    public function testGetEventDispatcherReturnsImmutable()
    {
        $dispatcher = $this->createStub(EventDispatcherInterface::class);

        $immutableDispatcher = $this->createBuilder(dispatcher: $dispatcher)->getEventDispatcher();

        $this->assertInstanceOf(ImmutableEventDispatcher::class, $immutableDispatcher);
        $this->assertSame($dispatcher, $this->getPrivatePropertyValue($immutableDispatcher, 'dispatcher'));
    }

    public function testGetName()
    {
        $this->assertSame('foo', $this->createBuilder()->getName());
    }

    public function testGetType()
    {
        $type = $this->createStub(ResolvedFilterTypeInterface::class);

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

    public function testGetHandlerWithoutHandlerSet()
    {
        $this->expectException(BadMethodCallException::class);

        $this->createBuilder()->getHandler();
    }

    public function testGetHandler()
    {
        $handler = $this->createStub(FilterHandlerInterface::class);

        $builder = $this->createBuilder();
        $builder->setHandler($handler);

        $this->assertSame($handler, $builder->getHandler());
    }

    public function testGetFormTypeDefault()
    {
        $this->assertSame(TextType::class, $this->createBuilder()->getFormType());
    }

    public function testGetFormType()
    {
        $builder = $this->createBuilder();
        $builder->setFormType(NumberType::class);

        $this->assertSame(NumberType::class, $builder->getFormType());
    }

    public function testGetFormOptions()
    {
        $builder = $this->createBuilder();
        $builder->setFormOptions(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $builder->getFormOptions());
    }

    public function testGetOperatorFormTypeDefault()
    {
        $this->assertSame(OperatorType::class, $this->createBuilder()->getOperatorFormType());
    }

    public function testGetOperatorFormType()
    {
        $builder = $this->createBuilder();
        $builder->setFormType(NumberType::class);

        $this->assertSame(NumberType::class, $builder->getFormType());
    }

    public function testGetOperatorFormOptions()
    {
        $builder = $this->createBuilder();
        $builder->setOperatorFormOptions(['foo' => 'bar']);

        $this->assertSame(['foo' => 'bar'], $builder->getOperatorFormOptions());
    }

    public function testGetSupportedOperatorMergesDefaultOperator()
    {
        $builder = $this->createBuilder();
        $builder->setSupportedOperators([Operator::Contains]);
        $builder->setDefaultOperator(Operator::NotContains);

        $this->assertSame([Operator::Contains, Operator::NotContains], $builder->getSupportedOperators());
    }

    public function testGetDefaultOperator()
    {
        $builder = $this->createBuilder();
        $builder->setDefaultOperator(Operator::NotContains);

        $this->assertSame(Operator::NotContains, $builder->getDefaultOperator());
    }

    public function testIsOperatorSelectable()
    {
        $builder = $this->createBuilder();

        $this->assertTrue($builder->setOperatorSelectable(true)->isOperatorSelectable());
        $this->assertFalse($builder->setOperatorSelectable(false)->isOperatorSelectable());
    }

    public function testGetEmptyDataDefault()
    {
        $this->assertEquals(new FilterData(), $this->createBuilder()->getEmptyData());
    }

    public function testGetEmptyData()
    {
        $emptyData = new FilterData('foo', Operator::Contains);

        $builder = $this->createBuilder();
        $builder->setEmptyData($emptyData);

        $this->assertSame($emptyData, $builder->getEmptyData());
    }

    public function testGetColumnConfig()
    {
        $config = $this->createBuilder()->getFilterConfig();

        $this->assertInstanceOf(FilterConfigInterface::class, $config);
        $this->assertTrue($this->getPrivatePropertyValue($config, 'locked'));
    }

    private function createBuilder(?ResolvedFilterTypeInterface $type = null, ?EventDispatcherInterface $dispatcher = null, array $options = []): FilterConfigBuilder
    {
        return new FilterConfigBuilder(
            name: 'foo',
            type: $type ?? $this->createStub(ResolvedFilterTypeInterface::class),
            dispatcher: $dispatcher ?? $this->createStub(EventDispatcherInterface::class),
            options: $options,
        );
    }
}

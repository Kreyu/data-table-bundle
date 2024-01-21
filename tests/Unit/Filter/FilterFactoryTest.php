<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterFactory;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\CustomFilterType;
use PHPUnit\Framework\TestCase;

class FilterFactoryTest extends TestCase
{
    private FilterFactoryInterface $factory;

    protected function setUp(): void
    {
        $this->factory = new FilterFactory(
            new FilterRegistry([], new ResolvedFilterTypeFactory()),
        );
    }

    public function testCreatingBuilder(): void
    {
        $builder = $this->factory->createBuilder();

        $this->assertEquals('filter', $builder->getName());
    }

    public function testCreatingBuilderWithType(): void
    {
        $builder = $this->factory->createBuilder(CustomFilterType::class);

        $this->assertEquals('custom_filter', $builder->getName());
        $this->assertEquals(CustomFilterType::class, $builder->getType()->getInnerType()::class);
    }

    public function testCreatingBuilderWithOptions(): void
    {
        $builder = $this->factory->createBuilder(CustomFilterType::class, ['foo' => 'bar']);

        $this->assertEquals('bar', $builder->getOption('foo'));
    }

    public function testCreatingNamedBuilder(): void
    {
        $builder = $this->factory->createNamedBuilder('foo');

        $this->assertEquals('foo', $builder->getName());
    }
}

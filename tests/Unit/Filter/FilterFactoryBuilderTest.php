<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterFactory;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryBuilder;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistryInterface;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\CustomFilterExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\CustomFilterType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\CustomFilterTypeExtension;
use PHPUnit\Framework\TestCase;

class FilterFactoryBuilderTest extends TestCase
{
    private FilterFactoryBuilderInterface $factoryBuilder;

    protected function setUp(): void
    {
        $this->factoryBuilder = new FilterFactoryBuilder();
    }

    public function testAddExtension(): void
    {
        $extension = new CustomFilterExtension();

        $this->factoryBuilder->addExtension($extension);

        $factory = $this->factoryBuilder->getFilterFactory();

        $registry = $this->getRegistry($factory);

        $extensions = $registry->getExtensions();

        $this->assertCount(1, $extensions);
        $this->assertEquals($extension, $extensions[0]);
    }

    public function testAddType(): void
    {
        $type = new CustomFilterType();

        $this->factoryBuilder->addType($type);

        $factory = $this->factoryBuilder->getFilterFactory();

        $registry = $this->getRegistry($factory);

        $extensions = $registry->getExtensions();

        $this->assertCount(1, $extensions);
        $this->assertTrue($extensions[0]->hasType($type::class));
    }

    public function testAddTypeExtension(): void
    {
        $typeExtension = new CustomFilterTypeExtension();

        $this->factoryBuilder->addTypeExtension($typeExtension);

        $factory = $this->factoryBuilder->getFilterFactory();

        $registry = $this->getRegistry($factory);

        $extensions = $registry->getExtensions();

        $this->assertCount(1, $extensions);
        $this->assertEquals([$typeExtension], $extensions[0]->getTypeExtensions(CustomFilterType::class));
    }

    private function getRegistry(FilterFactoryInterface $factory): FilterRegistryInterface
    {
        return (new \ReflectionProperty(FilterFactory::class, 'registry'))->getValue($factory);
    }
}

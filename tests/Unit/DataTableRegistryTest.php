<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit;

use Kreyu\Bundle\DataTableBundle\DataTableRegistry;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Extension\FooDataTableTypeBarExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Extension\FooDataTableTypeBazExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query\BarProxyQueryFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query\FooProxyQueryFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\BarDataTableType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\DataTableTypeWithSameParentType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\FooDataTableType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\RecursiveDataTableTypeBar;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\RecursiveDataTableTypeBaz;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\RecursiveDataTableTypeFoo;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactory;
use PHPUnit\Framework\TestCase;

class DataTableRegistryTest extends TestCase
{
    public function testGetType()
    {
        $resolvedType = $this->createRegistry()->getType(FooDataTableType::class);

        $this->assertInstanceOf(FooDataTableType::class, $resolvedType->getInnerType());
    }

    public function testGetTypeWithNonExistentType()
    {
        $this->expectException(InvalidArgumentException::class);

        // @phpstan-ignore-next-line
        $this->createRegistry()->getType('stdClass');
    }

    public function testGetTypeWithTypeExtensions()
    {
        $typeExtensions = [
            new FooDataTableTypeBarExtension(),
            new FooDataTableTypeBazExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(FooDataTableType::class);

        $this->assertSame($typeExtensions, $resolvedType->getTypeExtensions());
    }

    public function testGetTypeWithParent()
    {
        $resolvedType = $this->createRegistry()->getType(BarDataTableType::class);

        $this->assertInstanceOf(BarDataTableType::class, $resolvedType->getInnerType());
        $this->assertInstanceOf(FooDataTableType::class, $resolvedType->getParent()->getInnerType());
    }

    public function testGetTypeWithParentTypeExtensions()
    {
        $typeExtensions = [
            new FooDataTableTypeBarExtension(),
            new FooDataTableTypeBazExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(BarDataTableType::class);

        $this->assertEmpty($resolvedType->getTypeExtensions());
        $this->assertSame($typeExtensions, $resolvedType->getParent()->getTypeExtensions());
    }

    public function testTypeCannotHaveItselfAsParent()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Circular reference detected for data table type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\DataTableTypeWithSameParentType" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\DataTableTypeWithSameParentType > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\DataTableTypeWithSameParentType).');

        $registry = $this->createRegistry(types: [new DataTableTypeWithSameParentType()]);
        $registry->getType(DataTableTypeWithSameParentType::class);
    }

    public function testRecursiveTypeReferences()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Circular reference detected for data table type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\RecursiveDataTableTypeFoo" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\RecursiveDataTableTypeFoo > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\RecursiveDataTableTypeBar > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\RecursiveDataTableTypeBaz > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\RecursiveDataTableTypeFoo).');

        $registry = $this->createRegistry(types: [
            new RecursiveDataTableTypeFoo(),
            new RecursiveDataTableTypeBar(),
            new RecursiveDataTableTypeBaz(),
        ]);

        $registry->getType(RecursiveDataTableTypeFoo::class);
    }

    public function testHasType()
    {
        $this->assertTrue($this->createRegistry()->hasType(FooDataTableType::class));
    }

    public function testHasTypeWithNonExistentType()
    {
        // @phpstan-ignore-next-line
        $this->assertFalse($this->createRegistry()->hasType('stdClass'));
    }

    public function testGetProxyQueryFactories()
    {
        $proxyQueryFactories = [
            new FooProxyQueryFactory(),
            new BarProxyQueryFactory(),
        ];

        $registry = $this->createRegistry(proxyQueryFactories: $proxyQueryFactories);

        $this->assertSame($proxyQueryFactories, $registry->getProxyQueryFactories());
    }

    public function testCreatingRegistryWithInvalidType()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->createRegistry(types: [new \stdClass()]);
    }

    public function testCreatingRegistryWithInvalidTypeExtension()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->createRegistry(typeExtensions: [new \stdClass()]);
    }

    public function testCreatingRegistryWithInvalidProxyQueryFactory()
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->createRegistry(proxyQueryFactories: [new \stdClass()]);
    }

    private function createRegistry(array $types = [], array $typeExtensions = [], array $proxyQueryFactories = []): DataTableRegistry
    {
        return new DataTableRegistry(
            types: $types ?: [
                new DataTableType(),
                new FooDataTableType(),
                new BarDataTableType(),
            ],
            typeExtensions: $typeExtensions,
            proxyQueryFactories: $proxyQueryFactories,
            resolvedTypeFactory: new ResolvedDataTableTypeFactory(),
        );
    }
}

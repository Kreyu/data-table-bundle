<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Extension\SimpleColumnTypeBarExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Extension\SimpleColumnTypeFooExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\ColumnTypeWithSameParentType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\RecursiveColumnTypeBar;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\RecursiveColumnTypeBaz;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\RecursiveColumnTypeFoo;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\SimpleColumnType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\SimpleSubColumnType;
use PHPUnit\Framework\TestCase;

class ColumnRegistryTest extends TestCase
{
    public function testGetType()
    {
        $resolvedType = $this->createRegistry()->getType(SimpleColumnType::class);

        $this->assertInstanceOf(SimpleColumnType::class, $resolvedType->getInnerType());
    }

    public function testGetTypeWithNonExistentType()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createRegistry()->getType(NumberColumnType::class);
    }

    public function testGetTypeWithTypeExtensions()
    {
        $typeExtensions = [
            new SimpleColumnTypeFooExtension(),
            new SimpleColumnTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleColumnType::class);

        $this->assertSame($typeExtensions, $resolvedType->getTypeExtensions());
    }

    public function testGetTypeWithParent()
    {
        $resolvedType = $this->createRegistry()->getType(SimpleSubColumnType::class);

        $parentType = $resolvedType->getParent();

        $this->assertInstanceOf(SimpleSubColumnType::class, $resolvedType->getInnerType());
        $this->assertNotNull($parentType);
        $this->assertInstanceOf(SimpleColumnType::class, $parentType->getInnerType());
    }

    public function testGetTypeWithParentTypeExtensions()
    {
        $typeExtensions = [
            new SimpleColumnTypeFooExtension(),
            new SimpleColumnTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleSubColumnType::class);

        $parentType = $resolvedType->getParent();

        $this->assertEmpty($resolvedType->getTypeExtensions());
        $this->assertNotNull($parentType);
        $this->assertSame($typeExtensions, $parentType->getTypeExtensions());
    }

    public function testTypeCannotHaveItselfAsParent()
    {
        $this->expectExceptionObject(new LogicException(
            'Circular reference detected for column type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\ColumnTypeWithSameParentType" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\ColumnTypeWithSameParentType > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\ColumnTypeWithSameParentType).',
        ));

        $registry = $this->createRegistry(types: [new ColumnTypeWithSameParentType()]);
        $registry->getType(ColumnTypeWithSameParentType::class);
    }

    public function testRecursiveTypeReferences()
    {
        $this->expectExceptionObject(new LogicException(
            'Circular reference detected for column type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\RecursiveColumnTypeFoo" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\RecursiveColumnTypeFoo > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\RecursiveColumnTypeBar > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\RecursiveColumnTypeBaz > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\RecursiveColumnTypeFoo).',
        ));

        $registry = $this->createRegistry(types: [
            new RecursiveColumnTypeFoo(),
            new RecursiveColumnTypeBar(),
            new RecursiveColumnTypeBaz(),
        ]);

        $registry->getType(RecursiveColumnTypeFoo::class);
    }

    public function testHasType()
    {
        $this->assertTrue($this->createRegistry()->hasType(SimpleColumnType::class));
    }

    public function testHasTypeWithNonExistentType()
    {
        $this->assertFalse($this->createRegistry()->hasType(NumberColumnType::class));
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

    /**
     * @param list<object> $types
     * @param list<object> $typeExtensions
     */
    private function createRegistry(array $types = [], array $typeExtensions = []): ColumnRegistry
    {
        $defaultTypes = [
            new ColumnType(),
            new SimpleColumnType(),
            new SimpleSubColumnType(),
        ];

        return new ColumnRegistry(
            types: $types === [] ? $defaultTypes : $types,
            typeExtensions: $typeExtensions,
            resolvedTypeFactory: new ResolvedColumnTypeFactory(),
        );
    }
}

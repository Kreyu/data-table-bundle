<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\Type\CallbackFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Extension\SimpleFilterTypeBarExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Extension\SimpleFilterTypeFooExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\FilterTypeWithSameParentType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeBar;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeBaz;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeFoo;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\SimpleFilterType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\SimpleSubFilterType;
use PHPUnit\Framework\TestCase;

class FilterRegistryTest extends TestCase
{
    public function testGetType()
    {
        $resolvedType = $this->createRegistry()->getType(SimpleFilterType::class);

        $this->assertInstanceOf(SimpleFilterType::class, $resolvedType->getInnerType());
    }

    public function testGetTypeWithNonExistentType()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createRegistry()->getType(CallbackFilterType::class);
    }

    public function testGetTypeWithTypeExtensions()
    {
        $typeExtensions = [
            new SimpleFilterTypeFooExtension(),
            new SimpleFilterTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleFilterType::class);

        $this->assertSame($typeExtensions, $resolvedType->getTypeExtensions());
    }

    public function testGetTypeWithParent()
    {
        $resolvedType = $this->createRegistry()->getType(SimpleSubFilterType::class);

        $parentType = $resolvedType->getParent();

        $this->assertInstanceOf(SimpleSubFilterType::class, $resolvedType->getInnerType());
        $this->assertNotNull($parentType);
        $this->assertInstanceOf(SimpleFilterType::class, $parentType->getInnerType());
    }

    public function testGetTypeWithParentTypeExtensions()
    {
        $typeExtensions = [
            new SimpleFilterTypeFooExtension(),
            new SimpleFilterTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleSubFilterType::class);

        $parentType = $resolvedType->getParent();

        $this->assertEmpty($resolvedType->getTypeExtensions());
        $this->assertNotNull($parentType);
        $this->assertSame($typeExtensions, $parentType->getTypeExtensions());
    }

    public function testTypeCannotHaveItselfAsParent()
    {
        $this->expectExceptionObject(new LogicException(
            'Circular reference detected for filter type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\FilterTypeWithSameParentType" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\FilterTypeWithSameParentType > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\FilterTypeWithSameParentType).',
        ));

        $registry = $this->createRegistry(types: [new FilterTypeWithSameParentType()]);
        $registry->getType(FilterTypeWithSameParentType::class);
    }

    public function testRecursiveTypeReferences()
    {
        $this->expectExceptionObject(new LogicException(
            'Circular reference detected for filter type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeFoo" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeFoo > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeBar > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeBaz > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeFoo).',
        ));

        $registry = $this->createRegistry(types: [
            new RecursiveFilterTypeFoo(),
            new RecursiveFilterTypeBar(),
            new RecursiveFilterTypeBaz(),
        ]);

        $registry->getType(RecursiveFilterTypeFoo::class);
    }

    public function testHasType()
    {
        $this->assertTrue($this->createRegistry()->hasType(SimpleFilterType::class));
    }

    public function testHasTypeWithNonExistentType()
    {
        $this->assertFalse($this->createRegistry()->hasType(CallbackFilterType::class));
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
    private function createRegistry(array $types = [], array $typeExtensions = []): FilterRegistry
    {
        $defaultTypes = [
            new FilterType(),
            new SimpleFilterType(),
            new SimpleSubFilterType(),
        ];

        return new FilterRegistry(
            types: $types === [] ? $defaultTypes : $types,
            typeExtensions: $typeExtensions,
            resolvedTypeFactory: new ResolvedFilterTypeFactory(),
        );
    }
}

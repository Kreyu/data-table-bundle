<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Extension\SimpleFilterTypeFooExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Extension\SimpleFilterTypeBarExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\SimpleSubFilterType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\FilterTypeWithSameParentType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\SimpleFilterType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeBar;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeBaz;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeFoo;
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

        // @phpstan-ignore-next-line
        $this->createRegistry()->getType('stdClass');
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

        $this->assertInstanceOf(SimpleSubFilterType::class, $resolvedType->getInnerType());
        $this->assertInstanceOf(SimpleFilterType::class, $resolvedType->getParent()->getInnerType());
    }

    public function testGetTypeWithParentTypeExtensions()
    {
        $typeExtensions = [
            new SimpleFilterTypeFooExtension(),
            new SimpleFilterTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleSubFilterType::class);

        $this->assertEmpty($resolvedType->getTypeExtensions());
        $this->assertSame($typeExtensions, $resolvedType->getParent()->getTypeExtensions());
    }

    public function testTypeCannotHaveItselfAsParent()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Circular reference detected for filter type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\FilterTypeWithSameParentType" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\FilterTypeWithSameParentType > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\FilterTypeWithSameParentType).');

        $registry = $this->createRegistry(types: [new FilterTypeWithSameParentType()]);
        $registry->getType(FilterTypeWithSameParentType::class);
    }

    public function testRecursiveTypeReferences()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Circular reference detected for filter type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeFoo" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeFoo > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeBar > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeBaz > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\RecursiveFilterTypeFoo).');

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
        // @phpstan-ignore-next-line
        $this->assertFalse($this->createRegistry()->hasType('stdClass'));
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

    private function createRegistry(array $types = [], array $typeExtensions = []): FilterRegistry
    {
        return new FilterRegistry(
            types: $types ?: [
                new FilterType(),
                new SimpleFilterType(),
                new SimpleSubFilterType(),
            ],
            typeExtensions: $typeExtensions,
            resolvedTypeFactory: new ResolvedFilterTypeFactory(),
        );
    }
}

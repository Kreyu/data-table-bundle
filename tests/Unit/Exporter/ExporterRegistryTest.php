<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Exporter;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterRegistry;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\CallbackExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Extension\SimpleExporterTypeBarExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Extension\SimpleExporterTypeFooExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\ExporterTypeWithSameParentType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeBar;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeBaz;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeFoo;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\SimpleExporterType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\SimpleSubExporterType;
use PHPUnit\Framework\TestCase;

class ExporterRegistryTest extends TestCase
{
    public function testGetType()
    {
        $resolvedType = $this->createRegistry()->getType(SimpleExporterType::class);

        $this->assertInstanceOf(SimpleExporterType::class, $resolvedType->getInnerType());
    }

    public function testGetTypeWithNonExistentType()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->createRegistry()->getType(CallbackExporterType::class);
    }

    public function testGetTypeWithTypeExtensions()
    {
        $typeExtensions = [
            new SimpleExporterTypeFooExtension(),
            new SimpleExporterTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleExporterType::class);

        $this->assertSame($typeExtensions, $resolvedType->getTypeExtensions());
    }

    public function testGetTypeWithParent()
    {
        $resolvedType = $this->createRegistry()->getType(SimpleSubExporterType::class);

        $parentType = $resolvedType->getParent();

        $this->assertInstanceOf(SimpleSubExporterType::class, $resolvedType->getInnerType());
        $this->assertNotNull($parentType);
        $this->assertInstanceOf(SimpleExporterType::class, $parentType->getInnerType());
    }

    public function testGetTypeWithParentTypeExtensions()
    {
        $typeExtensions = [
            new SimpleExporterTypeFooExtension(),
            new SimpleExporterTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleSubExporterType::class);

        $parentType = $resolvedType->getParent();

        $this->assertEmpty($resolvedType->getTypeExtensions());
        $this->assertNotNull($parentType);
        $this->assertSame($typeExtensions, $parentType->getTypeExtensions());
    }

    public function testTypeCannotHaveItselfAsParent()
    {
        $this->expectExceptionObject(new LogicException(
            'Circular reference detected for exporter type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\ExporterTypeWithSameParentType" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\ExporterTypeWithSameParentType > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\ExporterTypeWithSameParentType).',
        ));

        $registry = $this->createRegistry(types: [new ExporterTypeWithSameParentType()]);
        $registry->getType(ExporterTypeWithSameParentType::class);
    }

    public function testRecursiveTypeReferences()
    {
        $this->expectExceptionObject(new LogicException(
            'Circular reference detected for exporter type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeFoo" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeFoo > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeBar > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeBaz > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeFoo).',
        ));

        $registry = $this->createRegistry(types: [
            new RecursiveExporterTypeFoo(),
            new RecursiveExporterTypeBar(),
            new RecursiveExporterTypeBaz(),
        ]);

        $registry->getType(RecursiveExporterTypeFoo::class);
    }

    public function testHasType()
    {
        $this->assertTrue($this->createRegistry()->hasType(SimpleExporterType::class));
    }

    public function testHasTypeWithNonExistentType()
    {
        $this->assertFalse($this->createRegistry()->hasType(CallbackExporterType::class));
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
    private function createRegistry(array $types = [], array $typeExtensions = []): ExporterRegistry
    {
        $defaultTypes = [
            new ExporterType(),
            new SimpleExporterType(),
            new SimpleSubExporterType(),
        ];

        return new ExporterRegistry(
            types: $types === [] ? $defaultTypes : $types,
            typeExtensions: $typeExtensions,
            resolvedTypeFactory: new ResolvedExporterTypeFactory(),
        );
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Exporter;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterRegistry;
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

        // @phpstan-ignore-next-line
        $this->createRegistry()->getType('stdClass');
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

        $this->assertInstanceOf(SimpleSubExporterType::class, $resolvedType->getInnerType());
        $this->assertInstanceOf(SimpleExporterType::class, $resolvedType->getParent()->getInnerType());
    }

    public function testGetTypeWithParentTypeExtensions()
    {
        $typeExtensions = [
            new SimpleExporterTypeFooExtension(),
            new SimpleExporterTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleSubExporterType::class);

        $this->assertEmpty($resolvedType->getTypeExtensions());
        $this->assertSame($typeExtensions, $resolvedType->getParent()->getTypeExtensions());
    }

    public function testTypeCannotHaveItselfAsParent()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Circular reference detected for exporter type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\ExporterTypeWithSameParentType" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\ExporterTypeWithSameParentType > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\ExporterTypeWithSameParentType).');

        $registry = $this->createRegistry(types: [new ExporterTypeWithSameParentType()]);
        $registry->getType(ExporterTypeWithSameParentType::class);
    }

    public function testRecursiveTypeReferences()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Circular reference detected for exporter type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeFoo" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeFoo > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeBar > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeBaz > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\RecursiveExporterTypeFoo).');

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

    private function createRegistry(array $types = [], array $typeExtensions = []): ExporterRegistry
    {
        return new ExporterRegistry(
            types: $types ?: [
                new ExporterType(),
                new SimpleExporterType(),
                new SimpleSubExporterType(),
            ],
            typeExtensions: $typeExtensions,
            resolvedTypeFactory: new ResolvedExporterTypeFactory(),
        );
    }
}

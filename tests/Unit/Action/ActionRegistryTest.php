<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Action;

use Kreyu\Bundle\DataTableBundle\Action\ActionRegistry;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactory;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Extension\SimpleActionTypeFooExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Extension\SimpleActionTypeBarExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\SimpleSubActionType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\ActionTypeWithSameParentType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\SimpleActionType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\RecursiveActionTypeBar;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\RecursiveActionTypeBaz;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\RecursiveActionTypeFoo;
use PHPUnit\Framework\TestCase;

class ActionRegistryTest extends TestCase
{
    public function testGetType()
    {
        $resolvedType = $this->createRegistry()->getType(SimpleActionType::class);

        $this->assertInstanceOf(SimpleActionType::class, $resolvedType->getInnerType());
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
            new SimpleActionTypeFooExtension(),
            new SimpleActionTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleActionType::class);

        $this->assertSame($typeExtensions, $resolvedType->getTypeExtensions());
    }

    public function testGetTypeWithParent()
    {
        $resolvedType = $this->createRegistry()->getType(SimpleSubActionType::class);

        $this->assertInstanceOf(SimpleSubActionType::class, $resolvedType->getInnerType());
        $this->assertInstanceOf(SimpleActionType::class, $resolvedType->getParent()->getInnerType());
    }

    public function testGetTypeWithParentTypeExtensions()
    {
        $typeExtensions = [
            new SimpleActionTypeFooExtension(),
            new SimpleActionTypeBarExtension(),
        ];

        $resolvedType = $this->createRegistry(typeExtensions: $typeExtensions)->getType(SimpleSubActionType::class);

        $this->assertEmpty($resolvedType->getTypeExtensions());
        $this->assertSame($typeExtensions, $resolvedType->getParent()->getTypeExtensions());
    }

    public function testTypeCannotHaveItselfAsParent()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Circular reference detected for action type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\ActionTypeWithSameParentType" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\ActionTypeWithSameParentType > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\ActionTypeWithSameParentType).');

        $registry = $this->createRegistry(types: [new ActionTypeWithSameParentType()]);
        $registry->getType(ActionTypeWithSameParentType::class);
    }

    public function testRecursiveTypeReferences()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Circular reference detected for action type "Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\RecursiveActionTypeFoo" (Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\RecursiveActionTypeFoo > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\RecursiveActionTypeBar > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\RecursiveActionTypeBaz > Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type\RecursiveActionTypeFoo).');

        $registry = $this->createRegistry(types: [
            new RecursiveActionTypeFoo(),
            new RecursiveActionTypeBar(),
            new RecursiveActionTypeBaz(),
        ]);

        $registry->getType(RecursiveActionTypeFoo::class);
    }

    public function testHasType()
    {
        $this->assertTrue($this->createRegistry()->hasType(SimpleActionType::class));
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

    private function createRegistry(array $types = [], array $typeExtensions = []): ActionRegistry
    {
        return new ActionRegistry(
            types: $types ?: [
                new ActionType(),
                new SimpleActionType(),
                new SimpleSubActionType(),
            ],
            typeExtensions: $typeExtensions,
            resolvedTypeFactory: new ResolvedActionTypeFactory(),
        );
    }
}

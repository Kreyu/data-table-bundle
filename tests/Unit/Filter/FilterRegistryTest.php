<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\CustomFilterType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\CustomFilterTypeExtension;
use PHPUnit\Framework\TestCase;

class FilterRegistryTest extends TestCase
{
    private function createRegistry(array $extensions = [], ?ResolvedFilterTypeFactoryInterface $resolvedTypeFactory = null): FilterRegistry
    {
        return new FilterRegistry($extensions, $resolvedTypeFactory ?? $this->createMock(ResolvedFilterTypeFactoryInterface::class));
    }

    public function testCallingGetTypeWithNonExistentClassThrowsException(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException('Could not load filter type "App\\InvalidFilterType": class does not exist.'));

        // @phpstan-ignore-next-line
        $this->createRegistry()->getType('App\\InvalidFilterType');
    }

    public function testCallingGetTypeWithInvalidClassThrowsException(): void
    {
        $this->expectExceptionObject(new InvalidArgumentException(sprintf('Could not load filter type "%s": class does not implement "%s".', FilterRegistry::class, FilterTypeInterface::class)));

        // @phpstan-ignore-next-line
        $this->createRegistry()->getType(FilterRegistry::class);
    }

    public function testGetTypeResolvesParentUsingExtension(): void
    {
        $filterType = new CustomFilterType();
        $filterTypeExtension = new CustomFilterTypeExtension();
        $parentFilterType = new FilterType();

        $extension = $this->createMock(FilterExtensionInterface::class);

        $extension
            ->expects($this->exactly(2))
            ->method('hasType')
            ->willReturnCallback(function (string $name) {
                return match ($name) {
                    CustomFilterType::class, FilterType::class => true,
                    default => false,
                };
            });

        $extension
            ->expects($this->exactly(2))
            ->method('getType')
            ->willReturnCallback(function (string $name) use ($filterType, $parentFilterType) {
                // @phpstan-ignore-next-line
                return match ($name) {
                    CustomFilterType::class => $filterType,
                    FilterType::class => $parentFilterType,
                };
            });

        $extension
            ->expects($this->exactly(2))
            ->method('getTypeExtensions')
            ->willReturnCallback(function (string $name) use ($filterTypeExtension) {
                return match ($name) {
                    CustomFilterType::class => [$filterTypeExtension],
                    default => [],
                };
            });

        $resolvedFilterTypeFactory = $this->createMock(ResolvedFilterTypeFactoryInterface::class);

        $resolvedFilterTypeFactory
            ->expects($matcher = $this->exactly(2))
            ->method('createResolvedType')
            ->willReturnCallback(function ($type, $typeExtensions, $parent) use ($matcher, $filterTypeExtension) {
                // @phpstan-ignore-next-line
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertInstanceOf(FilterType::class, $type),
                    2 => $this->assertInstanceOf(CustomFilterType::class, $type),
                };

                // @phpstan-ignore-next-line
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertEmpty($typeExtensions),
                    2 => $this->assertEquals([$filterTypeExtension], $typeExtensions),
                };

                // @phpstan-ignore-next-line
                match ($matcher->numberOfInvocations()) {
                    1 => $this->assertNull($parent),
                    2 => $this->assertInstanceOf(FilterTypeInterface::class, $parent->getInnerType()),
                };

                $resolvedFilterType = $this->createMock(ResolvedFilterTypeInterface::class);
                $resolvedFilterType->method('getInnerType')->willReturn($type);
                $resolvedFilterType->method('getParent')->willReturn($parent);

                return $resolvedFilterType;
            });

        $registry = $this->createRegistry([$extension], $resolvedFilterTypeFactory);

        $this->assertEquals($filterType, $registry->getType(CustomFilterType::class)->getInnerType());
        $this->assertEquals($parentFilterType, $registry->getType(CustomFilterType::class)->getParent()->getInnerType());
    }
}

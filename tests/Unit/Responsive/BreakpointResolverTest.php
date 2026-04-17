<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Responsive;

use Kreyu\Bundle\DataTableBundle\Responsive\BreakpointResolver;
use Kreyu\Bundle\DataTableBundle\Responsive\Device;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BreakpointResolverTest extends TestCase
{
    private BreakpointResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new BreakpointResolver([
            'sm' => 576,
            'md' => 768,
            'lg' => 992,
            'xl' => 1200,
        ]);
    }

    #[DataProvider('provideResolveCases')]
    public function testResolve(int $width, string $expected): void
    {
        $this->assertSame($expected, $this->resolver->resolve($width));
    }

    public static function provideResolveCases(): iterable
    {
        yield 'below sm' => [400, 'sm'];
        yield 'at sm boundary' => [576, 'sm'];
        yield 'between sm and md' => [700, 'md'];
        yield 'at md boundary' => [768, 'md'];
        yield 'between md and lg' => [900, 'lg'];
        yield 'at lg boundary' => [992, 'lg'];
        yield 'between lg and xl' => [1100, 'xl'];
        yield 'at xl boundary' => [1200, 'xl'];
        yield 'above xl' => [1920, 'xl'];
    }

    #[DataProvider('provideIsVisibleCases')]
    public function testIsVisible(string $activeBreakpoint, string $minimumBreakpoint, bool $expected): void
    {
        $this->assertSame($expected, $this->resolver->isVisible($activeBreakpoint, $minimumBreakpoint));
    }

    public static function provideIsVisibleCases(): iterable
    {
        // sm is the smallest breakpoint
        yield 'sm >= sm' => ['sm', 'sm', true];
        yield 'sm >= md' => ['sm', 'md', false];
        yield 'sm >= lg' => ['sm', 'lg', false];
        yield 'sm >= xl' => ['sm', 'xl', false];

        yield 'md >= sm' => ['md', 'sm', true];
        yield 'md >= md' => ['md', 'md', true];
        yield 'md >= lg' => ['md', 'lg', false];
        yield 'md >= xl' => ['md', 'xl', false];

        yield 'lg >= sm' => ['lg', 'sm', true];
        yield 'lg >= md' => ['lg', 'md', true];
        yield 'lg >= lg' => ['lg', 'lg', true];
        yield 'lg >= xl' => ['lg', 'xl', false];

        yield 'xl >= sm' => ['xl', 'sm', true];
        yield 'xl >= md' => ['xl', 'md', true];
        yield 'xl >= lg' => ['xl', 'lg', true];
        yield 'xl >= xl' => ['xl', 'xl', true];
    }

    public function testIsVisibleWithUnknownMinimumBreakpointReturnsFalse(): void
    {
        $this->assertFalse($this->resolver->isVisible('md', 'unknown'));
    }

    public function testIsVisibleWithUnknownActiveBreakpointReturnsFalse(): void
    {
        $this->assertFalse($this->resolver->isVisible('unknown', 'md'));
    }

    public function testBreakpointsAreSortedAutomatically(): void
    {
        $resolver = new BreakpointResolver([
            'xl' => 1200,
            'sm' => 576,
            'lg' => 992,
            'md' => 768,
        ]);

        // Despite unordered input, resolve should work correctly
        $this->assertSame('sm', $resolver->resolve(400));
        $this->assertSame('md', $resolver->resolve(700));
        $this->assertSame('lg', $resolver->resolve(900));
        $this->assertSame('xl', $resolver->resolve(1100));

        // isVisible should respect the sorted order
        $this->assertTrue($resolver->isVisible('lg', 'sm'));
        $this->assertFalse($resolver->isVisible('sm', 'lg'));
    }

    public function testHasBreakpoint(): void
    {
        $this->assertTrue($this->resolver->has('sm'));
        $this->assertTrue($this->resolver->has('xl'));
        $this->assertFalse($this->resolver->has('unknown'));
        $this->assertFalse($this->resolver->has(''));
    }

    #[DataProvider('provideUaFallbackCases')]
    public function testResolveUaFallback(Device $device, ?string $expected): void
    {
        $this->assertSame($expected, $this->resolver->resolveUaFallback($device));
    }

    public static function provideUaFallbackCases(): iterable
    {
        // 4 breakpoints: sm, md, lg, xl → floor(4/2) = 2 → index 2 = 'lg'
        yield 'phone → smallest (sm)' => [Device::Phone, 'sm'];
        yield 'tablet → median upper (lg)' => [Device::Tablet, 'lg'];
        yield 'desktop → largest (xl)' => [Device::Desktop, 'xl'];
    }

    public function testResolveUaFallbackWithEvenNumberOfBreakpoints(): void
    {
        // 4 breakpoints: sm, md, lg, xl → floor(4/2) = 2 → 'lg' (upper of the two middle)
        $this->assertSame('lg', $this->resolver->resolveUaFallback(Device::Tablet));
    }

    public function testResolveUaFallbackWithOddNumberOfBreakpoints(): void
    {
        $resolver = new BreakpointResolver([
            'sm' => 576,
            'md' => 768,
            'lg' => 992,
        ]);

        // 3 breakpoints: sm, md, lg → floor(3/2) = 1 → 'md' (true median)
        $this->assertSame('md', $resolver->resolveUaFallback(Device::Tablet));
    }

    public function testResolveUaFallbackWithTwoBreakpoints(): void
    {
        $resolver = new BreakpointResolver([
            'compact' => 600,
            'wide' => 1200,
        ]);

        // 2 breakpoints: compact, wide → floor(2/2) = 1 → 'wide' (upper of the pair)
        $this->assertSame('wide', $resolver->resolveUaFallback(Device::Tablet));
    }

    public function testResolveUaFallbackWithEmptyBreakpoints(): void
    {
        $resolver = new BreakpointResolver([]);

        $this->assertNull($resolver->resolveUaFallback(Device::Phone));
        $this->assertNull($resolver->resolveUaFallback(Device::Tablet));
        $this->assertNull($resolver->resolveUaFallback(Device::Desktop));
    }

    public function testGetBreakpoints(): void
    {
        $breakpoints = ['sm' => 576, 'md' => 768, 'lg' => 992, 'xl' => 1200];
        $resolver = new BreakpointResolver($breakpoints);

        $this->assertSame($breakpoints, $resolver->getBreakpoints());
    }

    public function testCustomBreakpointNames(): void
    {
        $resolver = new BreakpointResolver([
            'compact' => 480,
            'normal' => 960,
            'wide' => 1440,
        ]);

        $this->assertSame('compact', $resolver->resolve(300));
        $this->assertSame('normal', $resolver->resolve(700));
        $this->assertSame('wide', $resolver->resolve(1200));
        $this->assertSame('wide', $resolver->resolve(1920));

        $this->assertTrue($resolver->isVisible('wide', 'compact'));
        $this->assertFalse($resolver->isVisible('compact', 'wide'));
    }
}

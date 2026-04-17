<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Responsive;

class BreakpointResolver
{
    /** @var array<string, int> */
    private readonly array $breakpoints;

    /**
     * @param array<string, int> $breakpoints Associative array of name => max width
     */
    public function __construct(array $breakpoints)
    {
        asort($breakpoints);
        $this->breakpoints = $breakpoints;
    }

    /**
     * Resolves a pixel width to the name of the active breakpoint.
     *
     * Returns the largest configured breakpoint when width exceeds all thresholds.
     */
    public function resolve(int $width): string
    {
        foreach ($this->breakpoints as $name => $maxWidth) {
            if ($width <= $maxWidth) {
                return $name;
            }
        }

        return array_key_last($this->breakpoints);
    }

    /**
     * Checks whether a column is visible at the given active breakpoint.
     */
    public function isVisible(string $activeBreakpoint, string $minimumBreakpoint): bool
    {
        $names = array_keys($this->breakpoints);
        $activeIndex = array_search($activeBreakpoint, $names, true);
        $minimumIndex = array_search($minimumBreakpoint, $names, true);

        if (false === $activeIndex || false === $minimumIndex) {
            return false;
        }

        return $activeIndex >= $minimumIndex;
    }

    /**
     * Returns whether the given breakpoint name exists in the configuration.
     */
    public function has(string $name): bool
    {
        return isset($this->breakpoints[$name]);
    }

    /**
     * @return array<string, int>
     */
    public function getBreakpoints(): array
    {
        return $this->breakpoints;
    }

    /**
     * Resolves a User-Agent device type to a fallback breakpoint name.
     *
     * Phone → smallest breakpoint, Tablet → median breakpoint, Desktop → largest breakpoint.
     */
    public function resolveUaFallback(Device $device): ?string
    {
        $names = array_keys($this->breakpoints);

        if ([] === $names) {
            return null;
        }

        return match ($device) {
            Device::Phone => $names[0],
            Device::Tablet => $names[(int) floor(count($names) / 2)],
            Device::Desktop => $names[count($names) - 1],
        };
    }
}

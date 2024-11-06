<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Util;

/**
 * @internal
 */
class ArrayUtil
{
    /**
     * Maps an array with callable that returns an array of key-value pairs.
     *
     * <code>
     * $map = ArrayUtil::mapWithKeys(
     *     callback: fn (array $array) => [$array['id'] => $array['name']],
     *     array: [
     *         ['id' => 1, 'name' => 'John'],
     *         ['id' => 2, 'name' => 'Jane'],
     *     ]
     * );
     *
     * var_dump($map); // array(2) { [1] => string(4) "John" [2] => string(4) "Jane" }
     * </code>
     *
     * @param callable(mixed): array<int|string, mixed> $callback
     */
    public static function mapWithKeys(callable $callback, array $array): array
    {
        $data = [];

        foreach ($array as $value) {
            foreach ($callback($value) as $mapKey => $mapValue) {
                $data[$mapKey] = $mapValue;
            }
        }

        return $data;
    }

    /**
     * Performs a `ksort` on a copy of given array instead of modifying it by reference.
     */
    public static function ksort(array $array, int $flags = SORT_REGULAR): array
    {
        $copy = $array;

        ksort($copy, $flags);

        return $copy;
    }
}
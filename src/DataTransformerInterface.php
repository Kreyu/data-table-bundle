<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

/**
 * @template TValue
 * @template TTransformedValue
 */
interface DataTransformerInterface
{
    /**
     * @param TValue|null $value
     *
     * @psalm-return TTransformedValue|null
     */
    public function transform(mixed $value);
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures;

class StringableValue implements \Stringable
{
    public function __construct(
        private readonly mixed $value,
    ) {
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
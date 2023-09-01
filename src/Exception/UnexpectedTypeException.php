<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Exception;

class UnexpectedTypeException extends \InvalidArgumentException
{
    public function __construct(mixed $value, string $expectedType)
    {
        parent::__construct(sprintf('Expected argument of type %s, %s given', $expectedType, get_debug_type($value)));
    }
}

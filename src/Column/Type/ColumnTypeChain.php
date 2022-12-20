<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

class ColumnTypeChain
{
    private iterable $types = [];

    /**
     * @param iterable<ColumnTypeInterface> $types
     */
    public function __construct(iterable $types)
    {
        foreach ($types as $type) {
            $this->types[$type::class] = $type;
        }
    }

    public function get(string $typeClass): ?ColumnTypeInterface
    {
        return $this->types[$typeClass] ?? null;
    }
}

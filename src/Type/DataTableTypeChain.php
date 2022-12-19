<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

class DataTableTypeChain implements DataTableTypeChainInterface
{
    private iterable $types = [];

    /**
     * @param iterable<DataTableTypeInterface> $types
     */
    public function __construct(iterable $types)
    {
        foreach ($types as $type) {
            $this->types[get_class($type)] = $type;
        }
    }

    /**
     * @param class-string<DataTableTypeInterface> $typeClass
     */
    public function get(string $typeClass): ?DataTableTypeInterface
    {
        return $this->types[$typeClass] ?? null;
    }
}

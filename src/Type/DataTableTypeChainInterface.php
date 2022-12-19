<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

interface DataTableTypeChainInterface
{
    /**
     * @param class-string<DataTableTypeInterface> $typeClass
     */
    public function get(string $typeClass): ?DataTableTypeInterface;
}

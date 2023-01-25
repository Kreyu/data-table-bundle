<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;

interface DataTableRegistryInterface
{
    public function getType(string $name): ResolvedDataTableTypeInterface;
}

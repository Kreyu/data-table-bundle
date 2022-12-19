<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Factory;

use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;

interface FilterFactoryInterface
{
    /**
     * @param class-string<FilterInterface> $type
     */
    public function create(string $name, string $type, array $options = []): FilterInterface;
}

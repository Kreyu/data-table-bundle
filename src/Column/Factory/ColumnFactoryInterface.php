<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Factory;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;

interface ColumnFactoryInterface
{
    public function create(string $name, string $typeClass, array $options = []): ColumnInterface;
}

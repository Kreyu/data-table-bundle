<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

interface ColumnFactoryInterface
{
    public function create(string $name, string $type, array $options = []): ColumnInterface;
}

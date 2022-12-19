<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Mapper\Factory;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;

interface ColumnMapperFactoryInterface
{
    public function create(): ColumnMapperInterface;
}
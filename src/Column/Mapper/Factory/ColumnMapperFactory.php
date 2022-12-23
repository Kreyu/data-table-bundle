<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Mapper\Factory;

use Kreyu\Bundle\DataTableBundle\Column\Factory\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapper;
use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;

class ColumnMapperFactory implements ColumnMapperFactoryInterface
{
    public function __construct(
        private readonly ColumnFactoryInterface $columnFactory,
    ) {
    }

    public function create(): ColumnMapperInterface
    {
        return new ColumnMapper($this->columnFactory);
    }
}

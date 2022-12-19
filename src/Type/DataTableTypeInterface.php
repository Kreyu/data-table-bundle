<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapperInterface;

interface DataTableTypeInterface
{
    public function configureColumns(ColumnMapperInterface $columns): void;

    public function configureFilters(FilterMapperInterface $filters): void;
}

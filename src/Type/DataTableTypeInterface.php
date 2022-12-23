<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Column\Mapper\ColumnMapperInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Mapper\FilterMapperInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

interface DataTableTypeInterface
{
    public function createQuery(): ProxyQueryInterface;

    public function configureColumns(ColumnMapperInterface $columns): void;

    public function configureFilters(FilterMapperInterface $filters): void;

    public function getName(): ?string;
}

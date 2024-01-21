<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

interface FilterHandlerInterface
{
    public function handle(ProxyQueryInterface $query, FilterData $data, FilterInterface $filter): void;
}

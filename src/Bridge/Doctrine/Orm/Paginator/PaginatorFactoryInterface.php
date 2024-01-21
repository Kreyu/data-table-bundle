<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface PaginatorFactoryInterface
{
    public function create(QueryBuilder $queryBuilder, array $hints = []): Paginator;
}

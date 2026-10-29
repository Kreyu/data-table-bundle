<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;

interface PaginatorFactoryInterface
{
    /**
     * @param array<string, mixed> $hints
     */
    public function create(QueryBuilder $queryBuilder, Query $query, array $hints = []): OffsetPaginator;
}

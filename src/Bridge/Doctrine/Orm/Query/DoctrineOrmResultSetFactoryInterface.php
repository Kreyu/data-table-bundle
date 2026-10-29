<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;
use Kreyu\Bundle\DataTableBundle\Query\ResultSet;

interface DoctrineOrmResultSetFactoryInterface
{
    public function create(OffsetPaginator $paginator, Query $query, int $batchSize = 5000): ResultSet;
}

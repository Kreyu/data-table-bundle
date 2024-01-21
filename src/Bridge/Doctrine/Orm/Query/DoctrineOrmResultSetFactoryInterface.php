<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Kreyu\Bundle\DataTableBundle\Query\ResultSet;

interface DoctrineOrmResultSetFactoryInterface
{
    public function create(Paginator $paginator, int $batchSize = 5000): ResultSet;
}

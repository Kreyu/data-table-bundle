<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\QueryBuilder;

interface AliasResolverInterface
{
    public function resolve(string $queryPath, QueryBuilder $queryBuilder): string;
}

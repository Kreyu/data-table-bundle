<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\QueryBuilder;

class AliasResolver implements AliasResolverInterface
{
    public function resolve(string $queryPath, QueryBuilder $queryBuilder): string
    {
        if ($this->isResolvable($queryPath, $queryBuilder)) {
            $queryPath = current($queryBuilder->getRootAliases()).'.'.$queryPath;
        }

        return $queryPath;
    }

    private function isResolvable(string $path, QueryBuilder $queryBuilder): bool
    {
        if (str_contains($path, '.')) {
            return false;
        }

        foreach ($queryBuilder->getDQLPart('select') ?? [] as $select) {
            $parts = preg_split('/ as( hidden)? /i', $select->getParts()[0]);

            if ($path === ($parts[1] ?? $parts[0])) {
                return false;
            }
        }

        return true;
    }
}

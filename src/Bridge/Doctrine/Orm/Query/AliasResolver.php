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
            foreach ($select->getParts() as $clause) {
                preg_match_all('/([^,]+)\s+AS\s+(HIDDEN\s+)?([^,]+)?/i', $clause, $matches);

                if (in_array($path, $matches[3])) {
                    return false;
                }
            }
        }

        return true;
    }
}

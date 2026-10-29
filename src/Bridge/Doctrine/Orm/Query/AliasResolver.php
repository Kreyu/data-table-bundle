<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\QueryBuilder;

class AliasResolver implements AliasResolverInterface
{
    public function resolve(string $queryPath, QueryBuilder $queryBuilder): string
    {
        $isResolvable = $this->isResolvable($queryPath, $queryBuilder);

        if (!$isResolvable) {
            return $queryPath;
        }

        $rootAlias = current($queryBuilder->getRootAliases());

        if ($rootAlias === false) {
            return $queryPath;
        }

        return $rootAlias.'.'.$queryPath;
    }

    private function isResolvable(string $path, QueryBuilder $queryBuilder): bool
    {
        if (str_contains($path, '.')) {
            return false;
        }

        /** @var list<Select> $selects */
        $selects = $queryBuilder->getDQLPart('select');

        foreach ($selects as $select) {
            foreach ($select->getParts() as $clause) {
                $matches = [];
                preg_match_all('/([^,]+)\s+AS\s+(HIDDEN\s+)?([^,]+)?/i', (string) $clause, $matches);

                $isAliasedInSelect = in_array($path, $matches[3], strict: true);

                if ($isAliasedInSelect) {
                    return false;
                }
            }
        }

        return true;
    }
}

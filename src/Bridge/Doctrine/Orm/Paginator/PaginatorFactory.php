<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator;

use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\OrderBy;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\OffsetPaginator;

class PaginatorFactory implements PaginatorFactoryInterface
{
    /**
     * @param array<string, mixed> $hints
     */
    public function create(QueryBuilder $queryBuilder, Query $query, array $hints = []): OffsetPaginator
    {
        $rootEntity = current($queryBuilder->getRootEntities());

        if (false === $rootEntity) {
            throw new \RuntimeException('There are no root entities defined in the query.');
        }

        $identifierFieldNames = $queryBuilder
            ->getEntityManager()
            ->getClassMetadata($rootEntity)
            ->getIdentifierFieldNames()
        ;

        $hasSingleIdentifierName = 1 === \count($identifierFieldNames);
        /** @var array<string, list<Join>> $joinParts */
        $joinParts = $queryBuilder->getDQLPart('join');
        $hasJoins = \count($joinParts) > 0;

        if (!$hasJoins) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        /** @var mixed $value */
        foreach ($hints as $name => $value) {
            $query->setHint($name, $value);
        }

        // Paginator with fetchJoinCollection doesn't work with composite primary keys
        // https://github.com/doctrine/orm/issues/2910
        // To stay safe fetch join only when we have single primary key and joins
        $fetchJoinCollection = $hasSingleIdentifierName && $hasJoins;

        // it is only safe to disable output walkers for really simple queries
        $useOutputWalkers = null;

        if ($this->canDisableOutputWalkers($queryBuilder)) {
            $useOutputWalkers = false;
        }

        return new OffsetPaginator($fetchJoinCollection, $useOutputWalkers);
    }

    /**
     * @see https://github.com/doctrine/orm/issues/8278#issue-705517756
     */
    public function canDisableOutputWalkers(QueryBuilder $queryBuilder): bool
    {
        // Do not support queries using HAVING
        if (null !== $queryBuilder->getDQLPart('having')) {
            return false;
        }

        /** @var list<From> $fromParts */
        $fromParts = $queryBuilder->getDQLPart('from');

        // Do not support queries using multiple entities in FROM
        if (\count($fromParts) !== 1) {
            return false;
        }

        $fromPart = $fromParts[0];

        $classMetadata = $queryBuilder
            ->getEntityManager()
            ->getClassMetadata($fromPart->getFrom())
        ;

        $identifierFieldNames = $classMetadata->getIdentifierFieldNames();

        // Do not support entities using a composite identifier
        if (\count($identifierFieldNames) !== 1) {
            return false;
        }

        $identifierName = current($identifierFieldNames);

        if ($identifierName === false) {
            return false;
        }

        // Do not support entities using a foreign key as identifier
        if ($classMetadata->hasAssociation($identifierName)) {
            return false;
        }

        // Do not support queries using a field from a toMany relation in the ORDER BY clause
        if ($this->hasOrderByAssociation($queryBuilder)) {
            return false;
        }

        return true;
    }

    public function hasOrderByAssociation(QueryBuilder $queryBuilder): bool
    {
        /** @var array<string, list<Join>> $joinParts */
        $joinParts = $queryBuilder->getDQLPart('join');

        if ($joinParts === []) {
            return false;
        }

        /** @var list<OrderBy> $orderByParts */
        $orderByParts = $queryBuilder->getDQLPart('orderBy');

        if ($orderByParts === []) {
            return false;
        }

        $joinAliases = [];

        foreach ($joinParts as $joinPart) {
            foreach ($joinPart as $join) {
                $joinAlias = $join->getAlias();

                if ($joinAlias !== null) {
                    $joinAliases[] = $joinAlias;
                }
            }
        }

        foreach ($orderByParts as $orderByPart) {
            foreach ($orderByPart->getParts() as $part) {
                foreach ($joinAliases as $joinAlias) {
                    if (str_starts_with($part, $joinAlias.'.')) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}

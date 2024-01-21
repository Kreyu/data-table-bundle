<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorFactory implements PaginatorFactoryInterface
{
    public function create(QueryBuilder $queryBuilder, array $hints = []): Paginator
    {
        $rootEntity = current($queryBuilder->getRootEntities());

        if (false === $rootEntity) {
            throw new \RuntimeException('There are no root entities defined in the query.');
        }

        $identifierFieldNames = $queryBuilder
            ->getEntityManager()
            ->getClassMetadata($rootEntity)
            ->getIdentifierFieldNames();

        $hasSingleIdentifierName = 1 === \count($identifierFieldNames);
        $hasJoins = \count($queryBuilder->getDQLPart('join')) > 0;

        $query = $queryBuilder->getQuery();

        if (!$hasJoins) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        foreach ($hints as $name => $value) {
            $query->setHint($name, $value);
        }

        // Paginator with fetchJoinCollection doesn't work with composite primary keys
        // https://github.com/doctrine/orm/issues/2910
        // To stay safe fetch join only when we have single primary key and joins
        $paginator = new Paginator($query, $hasSingleIdentifierName && $hasJoins);

        // it is only safe to disable output walkers for really simple queries
        if ($this->canDisableOutputWalkers($queryBuilder)) {
            $paginator->setUseOutputWalkers(false);
        }

        return $paginator;
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

        $fromParts = $queryBuilder->getDQLPart('from');

        // Do not support queries using multiple entities in FROM
        if (1 !== \count($fromParts)) {
            return false;
        }

        $fromPart = current($fromParts);

        $classMetadata = $queryBuilder
            ->getEntityManager()
            ->getClassMetadata($fromPart->getFrom());

        $identifierFieldNames = $classMetadata->getIdentifierFieldNames();

        // Do not support entities using a composite identifier
        if (1 !== \count($identifierFieldNames)) {
            return false;
        }

        $identifierName = current($identifierFieldNames);

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
        $joinParts = $queryBuilder->getDQLPart('join');

        if (0 === \count($joinParts)) {
            return false;
        }

        $orderByParts = $queryBuilder->getDQLPart('orderBy');

        if (empty($orderByParts)) {
            return false;
        }

        $joinAliases = [];

        foreach ($joinParts as $joinPart) {
            foreach ($joinPart as $join) {
                $joinAliases[] = $join->getAlias();
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

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kreyu\Bundle\DataTableBundle\Pagination\Pagination;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;

class ProxyQuery implements ProxyQueryInterface
{
    private int $uniqueParameterId = 0;

    public function __construct(
        private QueryBuilder $queryBuilder,
    ) {
    }

    public function __call(string $name, array $args): mixed
    {
        return $this->queryBuilder->$name(...$args);
    }

    public function __get(string $name): mixed
    {
        return $this->queryBuilder->{$name};
    }

    public function __clone(): void
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }

    public function supports(mixed $data): bool
    {
        return $data instanceof QueryBuilder;
    }

    public function sort(string $field, string $direction): void
    {
        $this->queryBuilder->orderBy($field, $direction);
    }

    public function paginate(int $page, int $perPage): void
    {
        $this->queryBuilder
            ->setFirstResult($perPage * ($page - 1))
            ->setMaxResults($perPage);
    }

    public function getPagination(): PaginationInterface
    {
        $rootEntity = current($this->queryBuilder->getRootEntities());

        if (false === $rootEntity) {
            throw new \RuntimeException('There are not root entities defined in the query.');
        }

        $identifierFieldNames = $this->queryBuilder
            ->getEntityManager()
            ->getClassMetadata($rootEntity)
            ->getIdentifierFieldNames();

        $hasSingleIdentifierName = 1 === \count($identifierFieldNames);
        $hasJoins = \count($this->queryBuilder->getDQLPart('join')) > 0;

        $paginator = new Paginator($this->queryBuilder->getQuery(), $hasSingleIdentifierName && $hasJoins);

        return new Pagination(
            items: $paginator->getIterator(),
            currentPageNumber: $this->queryBuilder->getFirstResult() + 1,
            totalItemCount: $paginator->count(),
            itemNumberPerPage: $this->queryBuilder->getMaxResults(),
        );
    }

    public function getUniqueParameterId(): int
    {
        return $this->uniqueParameterId++;
    }
}
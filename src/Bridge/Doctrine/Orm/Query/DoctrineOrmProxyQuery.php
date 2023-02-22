<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kreyu\Bundle\DataTableBundle\Pagination\Pagination;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;

/**
 * @mixin QueryBuilder
 */
class DoctrineOrmProxyQuery implements ProxyQueryInterface
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

    public function sort(SortingData $sortingData): void
    {
        $rootAlias = current($this->queryBuilder->getRootAliases());

        foreach ($sortingData->getFields() as $field) {
            $fieldName = $field->getName();

            if ($rootAlias && !str_contains($fieldName, '.')) {
                $fieldName = $rootAlias . '.' . $fieldName;
            }

            $this->queryBuilder->orderBy($fieldName, $field->getDirection());
        }
    }

    public function paginate(PaginationData $paginationData): void
    {
        $this->queryBuilder
            ->setFirstResult($paginationData->getOffset())
            ->setMaxResults($paginationData->getPerPage())
        ;
    }

    /**
     * @throws \Exception
     */
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

        $currentPageNumber = (int) ($this->queryBuilder->getFirstResult() / $this->queryBuilder->getMaxResults()) + 1;

        return new Pagination(
            items: $paginator->getIterator(),
            currentPageNumber: $currentPageNumber,
            totalItemCount: $paginator->count(),
            itemNumberPerPage: $this->queryBuilder->getMaxResults(),
        );
    }

    public function getUniqueParameterId(): int
    {
        return $this->uniqueParameterId++;
    }
}

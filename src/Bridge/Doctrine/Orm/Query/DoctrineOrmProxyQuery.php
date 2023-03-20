<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
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

    /**
     * @param array<string, mixed> $hints
     */
    public function __construct(
        private QueryBuilder $queryBuilder,
        private array $hints = [],
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
                $fieldName = $rootAlias.'.'.$fieldName;
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

        $query = $this->queryBuilder->getQuery();

        if (!$hasJoins) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        foreach ($this->hints as $name => $value) {
            $query->setHint($name, $value);
        }

        $paginator = new Paginator($query, $hasSingleIdentifierName && $hasJoins);

        return new Pagination(
            items: $paginator->getIterator(),
            currentPageNumber: $this->getCurrentPageNumber(),
            totalItemCount: $paginator->count(),
            itemNumberPerPage: $this->queryBuilder->getMaxResults(),
        );
    }

    public function getUniqueParameterId(): int
    {
        return $this->uniqueParameterId++;
    }

    public function setHint(string $name, mixed $value): void
    {
        $this->hints[$name] = $value;
    }

    private function getCurrentPageNumber(): int
    {
        $firstResult = $this->queryBuilder->getFirstResult();
        $maxResults = $this->queryBuilder->getMaxResults() ?? 1;

        return (int) ($firstResult / $maxResults) + 1;
    }
}

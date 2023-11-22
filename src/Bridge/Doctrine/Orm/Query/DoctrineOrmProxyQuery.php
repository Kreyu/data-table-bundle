<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Kreyu\Bundle\DataTableBundle\Pagination\CurrentPageOutOfRangeException;
use Kreyu\Bundle\DataTableBundle\Pagination\Pagination;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;

/**
 * @mixin QueryBuilder
 */
class DoctrineOrmProxyQuery implements DoctrineOrmProxyQueryInterface
{
    private int $uniqueParameterId = 0;
    private int $batchSize = 5000;
    private bool $entityManagerClearingEnabled = true;

    /**
     * @param array<string, mixed> $hints
     */
    public function __construct(
        private QueryBuilder $queryBuilder,
        private array $hints = [],
        private string|int $hydrationMode = AbstractQuery::HYDRATE_OBJECT,
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

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function sort(SortingData $sortingData): void
    {
        $rootAlias = current($this->queryBuilder->getRootAliases());

        if (false === $rootAlias) {
            throw new \RuntimeException('There are no root aliases defined in the query.');
        }

        $this->queryBuilder->resetDQLPart('orderBy');

        foreach ($sortingData->getColumns() as $column) {
            $propertyPath = (string) $column->getPropertyPath();

            if ($rootAlias && !str_contains($propertyPath, '.') && !str_starts_with($propertyPath, '__')) {
                $propertyPath = $rootAlias.'.'.$propertyPath;
            }

            $this->queryBuilder->addOrderBy($propertyPath, $column->getDirection());
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
        $maxResults = $this->queryBuilder->getMaxResults();

        $paginator = $this->createPaginator(forceDisabledFetchJoinCollection: null === $maxResults);

        try {
            return new Pagination(
                items: $paginator->getIterator(),
                currentPageNumber: $this->getCurrentPageNumber(),
                totalItemCount: $paginator->count(),
                itemNumberPerPage: $maxResults,
            );
        } catch (CurrentPageOutOfRangeException) {
            $this->queryBuilder->setFirstResult(null);
        }

        return $this->getPagination();
    }

    public function getItems(): iterable
    {
        $paginator = $this->createPaginator(forceDisabledFetchJoinCollection: true);

        $batchSize = $this->batchSize;

        $cursorPosition = 0;

        do {
            $hasItems = true;

            if (0 === $cursorPosition % $batchSize) {
                $hasItems = false;

                $paginator->getQuery()->setMaxResults($batchSize);
                $paginator->getQuery()->setFirstResult($cursorPosition);

                foreach ($paginator->getIterator() as $item) {
                    $hasItems = true;
                    yield $item;
                }

                if ($this->entityManagerClearingEnabled) {
                    $this->getEntityManager()->clear();
                }
            }

            ++$cursorPosition;
        } while (0 === $cursorPosition || $hasItems);
    }

    public function getUniqueParameterId(): int
    {
        return $this->uniqueParameterId++;
    }

    public function setHint(string $name, mixed $value): void
    {
        $this->hints[$name] = $value;
    }

    public function setHydrationMode(int|string $hydrationMode): void
    {
        $this->hydrationMode = $hydrationMode;
    }

    public function isEntityManagerClearingEnabled(): bool
    {
        return $this->entityManagerClearingEnabled;
    }

    public function setEntityManagerClearingEnabled(bool $entityManagerClearingEnabled): void
    {
        $this->entityManagerClearingEnabled = $entityManagerClearingEnabled;
    }

    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    public function setBatchSize(int $batchSize): void
    {
        $this->batchSize = $batchSize;
    }

    private function getCurrentPageNumber(): int
    {
        $firstResult = $this->queryBuilder->getFirstResult();
        $maxResults = $this->queryBuilder->getMaxResults() ?? 1;

        return (int) ($firstResult / $maxResults) + 1;
    }

    private function createPaginator(bool $forceDisabledFetchJoinCollection = false): Paginator
    {
        $rootEntity = current($this->queryBuilder->getRootEntities());

        if (false === $rootEntity) {
            throw new \RuntimeException('There are no root entities defined in the query.');
        }

        $identifierFieldNames = $this->queryBuilder
            ->getEntityManager()
            ->getClassMetadata($rootEntity)
            ->getIdentifierFieldNames();

        $hasSingleIdentifierName = 1 === \count($identifierFieldNames);
        $hasJoins = \count($this->queryBuilder->getDQLPart('join')) > 0;

        $query = (clone $this->queryBuilder)->getQuery();

        $this->applyQueryHints($query);

        $query->setHydrationMode($this->hydrationMode);

        $fetchJoinCollection = $hasSingleIdentifierName && $hasJoins;

        if ($forceDisabledFetchJoinCollection) {
            $fetchJoinCollection = false;
        }

        return new Paginator($query, $fetchJoinCollection);
    }

    private function applyQueryHints(Query $query): void
    {
        foreach ($this->hints as $name => $value) {
            $query->setHint($name, $value);
        }
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator\PaginatorFactory;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator\PaginatorFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Query\ResultSetInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;

/**
 * @mixin QueryBuilder
 */
class DoctrineOrmProxyQuery implements DoctrineOrmProxyQueryInterface
{
    private int $uniqueParameterId = 0;
    private int $batchSize = 5000;
    private PaginatorFactoryInterface $paginatorFactory;
    private AliasResolverInterface $aliasResolver;
    private DoctrineOrmResultSetFactoryInterface $resultSetFactory;

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

    public function __clone(): void
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }

    public function sort(SortingData $sortingData): void
    {
        $this->queryBuilder->resetDQLPart('orderBy');

        foreach ($sortingData->getColumns() as $sortCriterion) {
            if ('none' === $sortCriterion->getDirection()) {
                continue;
            }

            $this->queryBuilder->addOrderBy(
                $this->getAliasResolver()->resolve((string) $sortCriterion->getPropertyPath(), $this->queryBuilder),
                $sortCriterion->getDirection(),
            );
        }
    }

    public function paginate(PaginationData $paginationData): void
    {
        $this->queryBuilder
            ->setFirstResult($paginationData->getOffset())
            ->setMaxResults($paginationData->getPerPage())
        ;
    }

    public function getResult(): ResultSetInterface
    {
        $paginator = $this->getPaginatorFactory()->create($this->queryBuilder, $this->hints);
        $paginator->getQuery()->setHydrationMode($this->hydrationMode);

        return $this->getResultSetFactory()->create($paginator, $this->batchSize);
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getUniqueParameterId(): int
    {
        return $this->uniqueParameterId++;
    }

    public function getHints(): array
    {
        return $this->hints;
    }

    public function setHint(string $name, mixed $value): void
    {
        $this->hints[$name] = $value;
    }

    public function getHydrationMode(): int|string
    {
        return $this->hydrationMode;
    }

    public function setHydrationMode(int|string $hydrationMode): void
    {
        $this->hydrationMode = $hydrationMode;
    }

    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    public function setBatchSize(int $batchSize): void
    {
        if ($batchSize <= 0) {
            throw new InvalidArgumentException('The batch size must be positive.');
        }

        $this->batchSize = $batchSize;
    }

    public function getPaginatorFactory(): PaginatorFactoryInterface
    {
        return $this->paginatorFactory ??= new PaginatorFactory();
    }

    public function setPaginatorFactory(PaginatorFactoryInterface $paginatorFactory): void
    {
        $this->paginatorFactory = $paginatorFactory;
    }

    public function getAliasResolver(): AliasResolverInterface
    {
        return $this->aliasResolver ??= new AliasResolver();
    }

    public function setAliasResolver(AliasResolverInterface $aliasResolver): void
    {
        $this->aliasResolver = $aliasResolver;
    }

    public function getResultSetFactory(): DoctrineOrmResultSetFactoryInterface
    {
        return $this->resultSetFactory ??= new DoctrineOrmResultSetFactory();
    }

    public function setResultSetFactory(DoctrineOrmResultSetFactoryInterface $resultSetFactory): void
    {
        $this->resultSetFactory = $resultSetFactory;
    }
}

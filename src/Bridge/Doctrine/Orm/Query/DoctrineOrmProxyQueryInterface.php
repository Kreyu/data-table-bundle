<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Paginator\PaginatorFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

/**
 * @mixin QueryBuilder
 */
interface DoctrineOrmProxyQueryInterface extends ProxyQueryInterface
{
    public function getQueryBuilder(): QueryBuilder;

    public function getUniqueParameterId(): int;

    /**
     * @return array<string, mixed>
     */
    public function getHints(): array;

    public function setHint(string $name, mixed $value): void;

    /**
     * @psalm-return string|AbstractQuery::HYDRATE_*
     */
    public function getHydrationMode(): int|string;

    /**
     * @psalm-param string|AbstractQuery::HYDRATE_* $hydrationMode
     */
    public function setHydrationMode(int|string $hydrationMode): void;

    public function getBatchSize(): int;

    public function setBatchSize(int $batchSize): void;

    public function getPaginatorFactory(): PaginatorFactoryInterface;

    public function setPaginatorFactory(PaginatorFactoryInterface $paginatorFactory): void;

    public function getAliasResolver(): AliasResolverInterface;

    public function setAliasResolver(AliasResolverInterface $aliasResolver): void;

    public function getResultSetFactory(): DoctrineOrmResultSetFactoryInterface;

    public function setResultSetFactory(DoctrineOrmResultSetFactoryInterface $resultSetFactory): void;
}

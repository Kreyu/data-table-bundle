<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

/**
 * @mixin QueryBuilder
 */
interface DoctrineOrmProxyQueryInterface extends ProxyQueryInterface
{
    public function getQueryBuilder(): QueryBuilder;

    public function getUniqueParameterId(): int;

    public function setHint(string $name, mixed $value): void;

    /**
     * @psalm-param string|AbstractQuery::HYDRATE_* $hydrationMode
     */
    public function setHydrationMode(int|string $hydrationMode): void;

    public function isEntityManagerClearingEnabled(): bool;

    public function setEntityManagerClearingEnabled(bool $entityManagerClearingEnabled): void;

    public function getBatchSize(): int;

    public function setBatchSize(int $batchSize): void;
}

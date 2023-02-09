<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Psr\Cache\InvalidArgumentException;

use function Symfony\Component\String\u;

use Symfony\Contracts\Cache\CacheInterface;

class CachePersistenceAdapter implements PersistenceAdapterInterface
{
    public function __construct(
        private CacheInterface $cache,
        private string $prefix,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function write(DataTableInterface $dataTable, PersistenceSubjectInterface $subject, mixed $data): void
    {
        $cacheKey = $this->getCacheKey($dataTable, $subject);

        $this->cache->delete($cacheKey);
        $this->cache->get($cacheKey, fn () => $data);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function read(DataTableInterface $dataTable, PersistenceSubjectInterface $subject, mixed $default = null): mixed
    {
        $cacheKey = $this->getCacheKey($dataTable, $subject);

        return $this->cache->get($cacheKey, fn () => $default);
    }

    private function getCacheKey(DataTableInterface $dataTable, PersistenceSubjectInterface $subject): string
    {
        $parts = [
            $dataTable->getConfig()->getName(),
            $this->prefix,
            $subject->getDataTablePersistenceIdentifier(),
        ];

        return u(implode('_', array_filter($parts)))->snake()->toString();
    }
}

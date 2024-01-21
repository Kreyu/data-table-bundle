<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

use function Symfony\Component\String\u;

class CachePersistenceAdapter implements PersistenceAdapterInterface
{
    public const TAG_PREFIX = 'kreyu_data_table_persistence_';

    public function __construct(
        private CacheInterface $cache,
        private string $prefix,
    ) {
    }

    public static function getTagName(PersistenceSubjectInterface $subject): string
    {
        return self::TAG_PREFIX.$subject->getDataTablePersistenceIdentifier();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function write(DataTableInterface $dataTable, PersistenceSubjectInterface $subject, mixed $data): void
    {
        $cacheKey = $this->getCacheKey($dataTable, $subject);

        $this->cache->delete($cacheKey);

        $this->getCacheValue($cacheKey, $this->getTagName($subject), $data);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function read(DataTableInterface $dataTable, PersistenceSubjectInterface $subject, mixed $default = null): mixed
    {
        $cacheKey = $this->getCacheKey($dataTable, $subject);

        return $this->getCacheValue($cacheKey, $this->getTagName($subject), $default);
    }

    private function getCacheKey(DataTableInterface $dataTable, PersistenceSubjectInterface $subject): string
    {
        return urlencode(implode('_', array_filter([
            $dataTable->getName(),
            $this->prefix,
            $subject->getDataTablePersistenceIdentifier(),
        ])));
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getCacheValue(string $key, string $tag, mixed $default = null): mixed
    {
        return $this->cache->get($key, function (ItemInterface $item) use ($tag, $default) {
            if ($this->cache instanceof TagAwareCacheInterface) {
                $item->tag($tag);
            }

            return $default;
        });
    }
}

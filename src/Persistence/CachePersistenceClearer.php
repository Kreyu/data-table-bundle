<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Persistence;

use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CachePersistenceClearer implements PersistenceClearerInterface
{
    public function __construct(
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function clear(PersistenceSubjectInterface $subject): void
    {
        if (!$this->cache instanceof TagAwareCacheInterface) {
            throw new \LogicException(sprintf('Cache instance must be an instance of %s', TagAwareCacheInterface::class));
        }

        $this->cache->invalidateTags([CachePersistenceAdapter::getTagName($subject)]);
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Persistence;

use Kreyu\Bundle\DataTableBundle\Persistence\CachePersistenceClearer;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectAggregate;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CachePersistenceClearerTest extends TestCase
{
    public function testClear()
    {
        $cache = $this->createMock(TagAwareCacheInterface::class);
        $cache->expects($this->once())->method('invalidateTags')->with(['kreyu_data_table_persistence_foo']);

        $clearer = new CachePersistenceClearer($cache);
        $clearer->clear(new PersistenceSubjectAggregate('foo', new \stdClass()));
    }

    public function testClearWithNonTagAwareCache()
    {
        $cache = $this->createMock(CacheInterface::class);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage(sprintf('Cache instance must be an instance of %s', TagAwareCacheInterface::class));

        $clearer = new CachePersistenceClearer($cache);
        $clearer->clear(new PersistenceSubjectAggregate('foo', new \stdClass()));
    }
}

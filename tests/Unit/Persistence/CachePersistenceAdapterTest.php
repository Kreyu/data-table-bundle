<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Persistence;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\CachePersistenceAdapter;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

class CachePersistenceAdapterTest extends TestCase
{
    public function testGetTagName()
    {
        $persistenceSubject = $this->createMock(PersistenceSubjectInterface::class);
        $persistenceSubject->method('getDataTablePersistenceIdentifier')->willReturn('foo');

        $this->assertEquals('kreyu_data_table_persistence_foo', CachePersistenceAdapter::getTagName($persistenceSubject));
    }

    public function testItUrlEncodesCacheKeyToPreventReservedCharactersError()
    {
        // The '%' is not reserved, but it should be encoded anyway to prevent overlapping of identifiers.
        // This would happen when, for example, one subject had identifier set to "@", and another had "%40" (already encoded, probably an edge case).
        $reservedCharacters = '{}()/\\@%';

        $cache = $this->createMock(CacheInterface::class);
        $cache->expects($this->exactly(2))->method('get')->with('products_%7B%7D%28%29%2F%5C%40%25_foo_%7B%7D%28%29%2F%5C%40%25_id_%7B%7D%28%29%2F%5C%40%25');

        $dataTable = $this->createMock(DataTableInterface::class);
        $dataTable->method('getName')->willReturn("products_$reservedCharacters");

        $persistenceSubject = $this->createMock(PersistenceSubjectInterface::class);
        $persistenceSubject->method('getDataTablePersistenceIdentifier')->willReturn("id_$reservedCharacters");

        $adapter = new CachePersistenceAdapter($cache, "foo_$reservedCharacters");
        $adapter->write($dataTable, $persistenceSubject, null);
        $adapter->read($dataTable, $persistenceSubject);
    }
}

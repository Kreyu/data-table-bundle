<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQuery;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\DoctrineOrmProxyQueryFactory;
use PHPUnit\Framework\TestCase;

class DoctrineOrmProxyQueryFactoryTest extends TestCase
{
    private DoctrineOrmProxyQueryFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new DoctrineOrmProxyQueryFactory();
    }

    public function testSupportsQueryBuilder()
    {
        $this->assertTrue($this->factory->supports($this->createStub(QueryBuilder::class)));
    }

    public function testNotSupportsQuery(): void
    {
        $this->assertFalse($this->factory->supports($this->createStub(Query::class)));
    }

    public function testCreate(): void
    {
        $queryBuilder = $this->createStub(QueryBuilder::class);

        $data = $this->factory->create($queryBuilder);

        $this->assertInstanceOf(DoctrineOrmProxyQuery::class, $data);
        $this->assertEquals($queryBuilder, $data->getQueryBuilder());
    }
}

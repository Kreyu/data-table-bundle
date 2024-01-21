<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Bridge\Doctrine\Orm\Query;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
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

    public function testCreatingWithSupportedData(): void
    {
        $queryBuilder = $this->createStub(QueryBuilder::class);

        $data = $this->factory->create($queryBuilder);

        $this->assertInstanceOf(DoctrineOrmProxyQuery::class, $data);
        $this->assertEquals($queryBuilder, $data->getQueryBuilder());
    }

    public function testCreatingWithNotSupportedData(): void
    {
        $data = $this->createStub(Query::class);

        $this->expectExceptionObject(new UnexpectedTypeException($data, QueryBuilder::class));

        $this->factory->create($data);
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Query;

use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQuery;
use Kreyu\Bundle\DataTableBundle\Query\ArrayProxyQueryFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArrayProxyQueryFactoryTest extends TestCase
{
    private ArrayProxyQueryFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new ArrayProxyQueryFactory();
    }

    public function testCreate()
    {
        $this->assertInstanceOf(ArrayProxyQuery::class, $this->factory->create([]));
    }

    #[DataProvider('provideSupportsCases')]
    public function testSupports(mixed $data, bool $expected)
    {
        $this->assertEquals($expected, $this->factory->supports($data));
    }

    public static function provideSupportsCases(): iterable
    {
        yield 'array' => [[], true];
        yield 'string' => ['', false];
        yield 'integer' => [123, false];
        yield 'bool' => [true, false];
        yield 'null' => [null, false];
        yield 'object' => [new \stdClass, false];
    }
}

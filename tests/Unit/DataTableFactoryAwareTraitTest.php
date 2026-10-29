<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit;

use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query\CustomProxyQuery;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\SimpleDataTableType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTableFactoryAwareController;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DataTableFactoryAwareTraitTest extends TestCase
{
    public function testCreateDataTable()
    {
        $query = new CustomProxyQuery();

        $factory = $this->createMock(DataTableFactoryInterface::class);
        $factory->expects($this->once())->method('create')->with(SimpleDataTableType::class, $query, ['bar' => 'baz']);

        $this->createController($factory)->createDataTable(SimpleDataTableType::class, $query, ['bar' => 'baz']);
    }

    public function testCreateNamedDataTable()
    {
        $query = new CustomProxyQuery();

        $factory = $this->createMock(DataTableFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('createNamed')
            ->with('foo', SimpleDataTableType::class, $query, ['bar' => 'baz'])
        ;

        $this->createController($factory)->createNamedDataTable('foo', SimpleDataTableType::class, $query, [
            'bar' => 'baz',
        ]);
    }

    public function testCreateDataTableBuilder()
    {
        $query = new CustomProxyQuery();

        $factory = $this->createMock(DataTableFactoryInterface::class);
        $factory->expects($this->once())->method('createBuilder')->with(DataTableType::class, $query, ['bar' => 'baz']);

        $this->createController($factory)->createDataTableBuilder($query, ['bar' => 'baz']);
    }

    public function testCreateNamedDataTableBuilder()
    {
        $query = new CustomProxyQuery();

        $factory = $this->createMock(DataTableFactoryInterface::class);
        $factory
            ->expects($this->once())
            ->method('createNamedBuilder')
            ->with('foo', DataTableType::class, $query, ['bar' => 'baz'])
        ;

        $this->createController($factory)->createNamedDataTableBuilder('foo', $query, ['bar' => 'baz']);
    }

    /**
     * @param \Closure(DataTableFactoryAwareController): mixed $execute
     */
    #[DataProvider('provideTraitCreateMethods')]
    public function testExecutingMethodsWithoutFactoryThrows(string $method, \Closure $execute)
    {
        $this->expectExceptionObject(new LogicException(sprintf(
            'You cannot use the "%s" method on controller without data table factory.',
            DataTableFactoryAwareTrait::class.'::'.$method,
        )));

        $execute($this->createController());
    }

    public static function provideTraitCreateMethods(): iterable
    {
        yield 'createDataTable' => [
            'createDataTable',
            static fn (DataTableFactoryAwareController $controller) => $controller->createDataTable(
                DataTableType::class,
            ),
        ];

        yield 'createNamedDataTable' => [
            'createNamedDataTable',
            static fn (DataTableFactoryAwareController $controller) => $controller->createNamedDataTable(
                'foo',
                DataTableType::class,
            ),
        ];

        yield 'createDataTableBuilder' => [
            'createDataTableBuilder',
            static fn (DataTableFactoryAwareController $controller) => $controller->createDataTableBuilder(),
        ];

        yield 'createNamedDataTableBuilder' => [
            'createNamedDataTableBuilder',
            static fn (DataTableFactoryAwareController $controller) => $controller->createNamedDataTableBuilder('foo'),
        ];
    }

    private function createController(?DataTableFactoryInterface $factory = null): DataTableFactoryAwareController
    {
        $controller = new DataTableFactoryAwareController();
        $controller->setDataTableFactory($factory);

        return $controller;
    }
}

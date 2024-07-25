<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit;

use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\DataTableFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query\CustomProxyQuery;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type\SimpleDataTableType;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DataTableFactoryAwareTraitTest extends TestCase
{
    public function testCreateDataTable()
    {
        $arguments = [SimpleDataTableType::class, new CustomProxyQuery(), ['bar' => 'baz']];

        $factory = $this->createMock(DataTableFactoryInterface::class);
        $factory->expects($this->once())->method('create')->with(...$arguments);

        $this->createClassUsingTrait($factory)->execute('createDataTable', $arguments);
    }

    public function testCreateNamedDataTable()
    {
        $arguments = ['foo', SimpleDataTableType::class, new CustomProxyQuery(), ['bar' => 'baz']];

        $factory = $this->createMock(DataTableFactoryInterface::class);
        $factory->expects($this->once())->method('createNamed')->with(...$arguments);

        $this->createClassUsingTrait($factory)->execute('createNamedDataTable', $arguments);
    }

    public function testCreateDataTableBuilder()
    {
        $arguments = [new CustomProxyQuery(), ['bar' => 'baz']];

        $factory = $this->createMock(DataTableFactoryInterface::class);
        $factory->expects($this->once())->method('createBuilder')->with(DataTableType::class, ...$arguments);

        $this->createClassUsingTrait($factory)->execute('createDataTableBuilder', $arguments);
    }

    public function testCreateNamedDataTableBuilder()
    {
        $arguments = ['foo', new CustomProxyQuery(), ['bar' => 'baz']];

        $factory = $this->createMock(DataTableFactoryInterface::class);
        $factory->expects($this->once())->method('createNamedBuilder')->with($arguments[0], DataTableType::class, $arguments[1], $arguments[2]);

        $this->createClassUsingTrait($factory)->execute('createNamedDataTableBuilder', $arguments);
    }

    #[DataProvider('provideTraitCreateMethods')]
    public function testExecutingMethodsWithoutFactoryThrows(string $method, array $arguments)
    {
        $this->expectExceptionMessage(vsprintf('You cannot use the "%s" method on controller without data table factory.', [
            DataTableFactoryAwareTrait::class.'::'.$method,
        ]));

        $this->createClassUsingTrait()->execute($method, $arguments);
    }

    public static function provideTraitCreateMethods(): iterable
    {
        yield 'createDataTable' => ['createDataTable', [DataTableType::class]];
        yield 'createNamedDataTable' => ['createNamedDataTable', ['foo', DataTableType::class]];
        yield 'createDataTableBuilder' => ['createDataTableBuilder', []];
        yield 'createNamedDataTableBuilder' => ['createNamedDataTableBuilder', ['foo']];
    }

    private function createClassUsingTrait(?DataTableFactoryInterface $factory = null): object
    {
        $class = new class() {
            use DataTableFactoryAwareTrait;

            public function execute(string $method, array $arguments): void
            {
                $this->{$method}(...$arguments);
            }
        };

        $class->setDataTableFactory($factory);

        return $class;
    }
}

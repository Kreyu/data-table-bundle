<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTables;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\BadMethodCallException;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

trait ColumnTypeTestCaseTrait
{
    private ColumnFactoryInterface $columnFactory;

    /**
     * @return class-string<ColumnTypeInterface>
     */
    abstract protected function getTestedType(): string;

    protected function createColumn(array $options = []): ColumnInterface
    {
        return $this->getColumnFactory()->create($this->getTestedType(), $options);
    }

    protected function createNamedColumn(string $name, array $options = []): ColumnInterface
    {
        return $this->getColumnFactory()->createNamed($name, $this->getTestedType(), $options);
    }

    protected function getColumnFactory(): ColumnFactoryInterface
    {
        return $this->columnFactory ??= DataTables::createColumnFactoryBuilder()
            ->getColumnFactory();
    }

    protected function createHeaderRowView(): HeaderRowView
    {
        return new HeaderRowView(new DataTableView());
    }

    protected function createValueRowView(mixed $data, int $index = 0): ValueRowView
    {
        return new ValueRowView(new DataTableView(), $index, $data);
    }

    protected function createDataTableMock(): MockObject&DataTableInterface
    {
        if (!method_exists($this, 'createMock')) {
            throw new BadMethodCallException(sprintf('The %s trait should be used in class extending the %s class', __TRAIT__, TestCase::class));
        }

        return $this->createMock(DataTableInterface::class);
    }
}

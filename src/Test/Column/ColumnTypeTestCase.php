<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;

abstract class ColumnTypeTestCase extends ColumnIntegrationTestCase
{
    /**
     * @return class-string<ColumnTypeInterface>
     */
    abstract protected function getTestedType(): string;

    protected function createColumn(array $options = []): ColumnInterface
    {
        $column = $this->factory->create($this->getTestedType(), $options);
        $column->setDataTable($this->createMock(DataTableInterface::class));

        return $column;
    }

    protected function createNamedColumn(string $name, array $options = []): ColumnInterface
    {
        $column = $this->factory->createNamed($name, $this->getTestedType(), $options);
        $column->setDataTable($this->createMock(DataTableInterface::class));

        return $column;
    }

    protected function createHeaderRowViewMock(): HeaderRowView
    {
        $headerRow = $this->createMock(HeaderRowView::class);
        $headerRow->parent = $this->createDataTableViewMock();

        return $headerRow;
    }

    protected function createValueRowViewMock(mixed $data = null): ValueRowView
    {
        $valueRow = $this->createMock(ValueRowView::class);
        $valueRow->parent = $this->createDataTableViewMock();
        $valueRow->data = $data;

        return $valueRow;
    }

    protected function createDataTableViewMock()
    {
        return $this->createMock(DataTableView::class);
    }
}

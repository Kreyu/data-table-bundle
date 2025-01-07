<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\LinkColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;

class LinkColumnTypeTest extends ColumnTypeTestCase
{
    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new LinkColumnType();
    }

    protected function getAdditionalColumnTypes(): array
    {
        return [
            new TextColumnType(),
            new ColumnType(),
        ];
    }

    public function testPassingHrefOptionAsString(): void
    {
        $column = $this->createColumn([
            'href' => 'https://example.com',
        ]);

        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals('https://example.com', $columnValueView->vars['href']);
    }

    public function testPassingHrefOptionAsCallable(): void
    {
        $rowData = new class {
            public string $link = 'foo';
        };

        $column = $this->createColumn([
            'href' => function (string $columnData, mixed $passedRowData, ColumnInterface $passedColumn) use (&$column, $rowData) {
                $this->assertEquals('foo', $columnData);
                $this->assertEquals($rowData, $passedRowData);
                $this->assertEquals($column, $passedColumn);

                return 'https://example.com';
            },
        ]);

        $columnValueView = $this->createColumnValueView($column, data: 'foo', rowData: $rowData);

        $this->assertEquals('https://example.com', $columnValueView->vars['href']);
    }

    public function testPassingTargetOptionAsString(): void
    {
        $column = $this->createColumn([
            'target' => '_blank',
        ]);

        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals('_blank', $columnValueView->vars['target']);
    }

    public function testPassingTargetOptionAsCallable(): void
    {
        $rowData = new class {
            public string $link = 'foo';
        };

        $column = $this->createColumn([
            'target' => function (string $columnData, mixed $passedRowData, ColumnInterface $passedColumn) use (&$column, $rowData) {
                $this->assertEquals('foo', $columnData);
                $this->assertEquals($rowData, $passedRowData);
                $this->assertEquals($column, $passedColumn);

                return '_blank';
            },
        ]);

        $columnValueView = $this->createColumnValueView($column, data: 'foo', rowData: $rowData);

        $this->assertEquals('_blank', $columnValueView->vars['target']);
    }
}

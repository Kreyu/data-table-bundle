<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Column\Type\CollectionColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;

class CollectionColumnTypeTest extends ColumnTypeTestCase
{
    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new CollectionColumnType();
    }

    protected function getAdditionalColumnTypes(): array
    {
        return [
            new NumberColumnType(),
            new TextColumnType(),
            new ColumnType(),
        ];
    }

    public function testPassingSeparatorOptionAsString(): void
    {
        $column = $this->createColumn([
            'separator' => '|',
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertEquals('|', $valueView->vars['separator']);
    }

    public function testPassingSeparatorOptionAsNull(): void
    {
        $column = $this->createColumn([
            'separator' => null,
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertNull($valueView->vars['separator']);
    }

    public function testCreatesChildren(): void
    {
        $column = $this->createColumn([
            'entry_type' => NumberColumnType::class,
            'entry_options' => [
                'use_intl_formatter' => false,
            ],
        ]);

        $data = new class {
            public array $collection = [1, 2, 3];
        };

        $valueRowView = $this->createValueRowView(data: $data);
        $columnValueView = $this->createColumnValueView($column, $valueRowView);

        $this->assertCount(3, $columnValueView->vars['children']);
        $this->assertContainsOnlyInstancesOf(ColumnValueView::class, $columnValueView->vars['children']);

        for ($i = 0; $i <= 2; ++$i) {
            /** @var ColumnValueView $child */
            $child = $columnValueView->vars['children'][$i];

            $expectedValueRowView = clone $valueRowView;
            $expectedValueRowView->origin = $valueRowView;
            $expectedValueRowView->index = $i;
            $expectedValueRowView->data = $data->collection[$i];

            $this->assertEquals((string) $i, $child->vars['name']);
            $this->assertEquals($expectedValueRowView, $child->vars['row']);
            $this->assertFalse($child->vars['use_intl_formatter']);
            $this->assertEquals($child->getDataTable(), $columnValueView->getDataTable());
        }
    }
}

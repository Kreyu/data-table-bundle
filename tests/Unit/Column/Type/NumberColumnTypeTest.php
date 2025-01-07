<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;

class NumberColumnTypeTest extends ColumnTypeTestCase
{
    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new NumberColumnType();
    }

    protected function getAdditionalColumnTypes(): array
    {
        return [
            new TextColumnType(),
            new ColumnType(),
        ];
    }

    public function testUseIntlFormatterOption(): void
    {
        $column = $this->createColumn([
            'use_intl_formatter' => true,
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertTrue($valueView->vars['use_intl_formatter']);
    }

    public function testIntlFormatterOptionsOption(): void
    {
        $column = $this->createColumn([
            'intl_formatter_options' => [
                'attrs' => ['foo' => 'bar'],
                'style' => 'currency',
            ],
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertSame(['foo' => 'bar'], $valueView->vars['intl_formatter_options']['attrs']);
        $this->assertSame('currency', $valueView->vars['intl_formatter_options']['style']);
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\MoneyColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;

class MoneyColumnTypeTest extends ColumnTypeTestCase
{
    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new MoneyColumnType();
    }

    protected function getAdditionalColumnTypes(): array
    {
        return [
            new NumberColumnType(),
            new TextColumnType(),
            new ColumnType(),
        ];
    }

    public function testCurrencyOptionAsString(): void
    {
        $column = $this->createColumn([
            'currency' => 'PLN',
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertEquals('PLN', $valueView->vars['currency']);
    }
}

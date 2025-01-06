<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\BooleanColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;

class BooleanColumnTypeTest extends ColumnTypeTestCase
{
    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new BooleanColumnType();
    }

    public function testLabelTrueOption(): void
    {
        $column = $this->createColumn([
            'label_true' => 'True',
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertEquals('True', $valueView->vars['label_true']);
    }

    public function testLabelFalseOption(): void
    {
        $column = $this->createColumn([
            'label_false' => 'False',
        ]);

        $valueView = $this->createColumnValueView($column);

        $this->assertEquals('False', $valueView->vars['label_false']);
    }
}

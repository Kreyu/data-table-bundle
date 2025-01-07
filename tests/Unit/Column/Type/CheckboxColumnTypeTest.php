<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;

class CheckboxColumnTypeTest extends ColumnTypeTestCase
{
    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new CheckboxColumnType();
    }

    public function testPassingIdentifierNameOption(): void
    {
        $column = $this->createColumn([
            'identifier_name' => 'uuid',
        ]);

        $columnHeaderView = $this->createColumnHeaderView($column);
        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals('uuid', $columnHeaderView->vars['identifier_name']);
        $this->assertEquals('uuid', $columnValueView->vars['identifier_name']);
    }
}

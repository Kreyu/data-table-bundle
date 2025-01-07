<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\TemplateColumnType;
use Kreyu\Bundle\DataTableBundle\Test\Column\Type\ColumnTypeTestCase;

class TemplateColumnTypeTest extends ColumnTypeTestCase
{
    protected function getTestedColumnType(): ColumnTypeInterface
    {
        return new TemplateColumnType();
    }

    protected function getAdditionalColumnTypes(): array
    {
        return [
            new ColumnType(),
        ];
    }

    public function testTemplatePathOptionAsString()
    {
        $column = $this->createColumn(['template_path' => 'template.html.twig']);

        $valueView = $this->createColumnValueView($column);

        $this->assertEquals('template.html.twig', $valueView->vars['template_path']);
    }

    public function testTemplatePathOptionAsCallable()
    {
        $column = $this->createColumn([
            'template_path' => function (string $rowData, ColumnInterface $passedColumn) use (&$column) {
                $this->assertEquals('foo', $rowData);
                $this->assertSame($column, $passedColumn);

                return 'template.html.twig';
            },
        ]);

        $columnValueView = $this->createColumnValueView($column, rowData: 'foo');

        $this->assertEquals('template.html.twig', $columnValueView->vars['template_path']);
    }

    public function testTemplateVarsOptionAsArray()
    {
        $column = $this->createColumn([
            'template_path' => 'template.html.twig',
            'template_vars' => [
                'foo' => 'bar',
            ],
        ]);

        $columnValueView = $this->createColumnValueView($column);

        $this->assertEquals(['foo' => 'bar'], $columnValueView->vars['template_vars']);
    }

    public function testTemplateVarsOptionAsCallable()
    {
        $column = $this->createColumn([
            'template_path' => 'template.html.twig',
            'template_vars' => function (string $rowData, ColumnInterface $passedColumn) use (&$column) {
                $this->assertEquals('foo', $rowData);
                $this->assertSame($column, $passedColumn);

                return ['foo' => 'bar'];
            },
        ]);

        $columnValueView = $this->createColumnValueView($column, rowData: 'foo');

        $this->assertEquals(['foo' => 'bar'], $columnValueView->vars['template_vars']);
    }
}

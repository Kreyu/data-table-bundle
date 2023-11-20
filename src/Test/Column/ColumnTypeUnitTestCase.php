<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

abstract class ColumnTypeUnitTestCase extends TestCase
{
    use ColumnTypeTestCaseTrait;

    /**
     * @return iterable<array{0: array<string, mixed>, 1: array<string, mixed>}>
     */
    public static function optionsToHeaderViewVarsProvider(): iterable
    {
        return [];
    }

    /**
     * @return iterable<array{0: array<string, mixed>, 1: array<string, mixed>}>
     */
    public static function optionsToValueViewVarsProvider(): iterable
    {
        return [];
    }

    /**
     * @dataProvider optionsToHeaderViewVarsProvider
     */
    public function testPassingOptionsResultsInHeaderViewVars(array $options, array $headerViewVars = null): void
    {
        $column = $this->createColumn($options);

        $view = $column->createHeaderView($this->createHeaderRowView());

        foreach ($headerViewVars ?? $options as $key => $value) {
            $this->assertEquals($value, $view->vars[$key]);
        }
    }

    /**
     * @dataProvider optionsToValueViewVarsProvider
     */
    public function testPassingOptionsResultsInValueViewVars(array $options, array $valueViewVars = null, mixed $valueRowData = null): void
    {
        $column = $this->createColumn($options);

        $view = $column->createValueView($this->createValueRowView(data: $valueRowData));

        foreach ($valueViewVars ?? $options as $key => $value) {
            $this->assertEquals($value, $view->vars[$key]);
        }
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Test\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableConfigInterface;
use Kreyu\Bundle\DataTableBundle\DataTables;
use Kreyu\Bundle\DataTableBundle\Test\RenderingTestCase;
use Kreyu\Bundle\DataTableBundle\Twig\DataTableExtension;

abstract class ColumnTypeRenderingTestCase extends RenderingTestCase
{
    use ColumnTypeTestCaseTrait;

    /**
     * Data provider used to execute the {@see static::testRenderingColumnHeader} test.
     * Order of the entries doesn't matter as they get resolved.
     *
     * @return iterable<array{
     *     theme: string,
     *     column: array{
     *         name: string|null,
     *         data: mixed,
     *         options: array
     *     },
     *     expectedHtml: string
     * }>
     */
    public static function columnHeaderProvider(): iterable
    {
        return [];
    }

    /**
     * Data provider used to execute the {@see static::testRenderingColumnValue} test.
     * Order of the entries doesn't matter as they get resolved.
     *
     * @return iterable<array{
     *     theme: string,
     *     column: array{
     *         name: string|null,
     *         data: mixed,
     *         options: array
     *     },
     *     expectedHtml: string
     * }>
     */
    public static function columnValueProvider(): iterable
    {
        return [];
    }

    final public static function resolvedColumnHeaderProvider(): iterable
    {
        yield from static::resolveProviderEntries(static::columnHeaderProvider());
    }

    final public static function resolvedColumnValueProvider(): iterable
    {
        yield from static::resolveProviderEntries(static::columnValueProvider());
    }

    /**
     * @dataProvider resolvedColumnHeaderProvider
     */
    public function testRenderingColumnHeader(
        string $theme,
        array  $column = [],
        array  $row = [],
        array  $dataTable = [],
        string $expectedHtml = null
    ): void {
        $dataTableConfig = $this->createMock(DataTableConfigInterface::class);
        $dataTableConfig->method('getSortParameterName')->willReturn(DataTableConfigInterface::SORT_PARAMETER);

        $dataTable = $this->createDataTableMock();
        $dataTable->method('getConfig')->willReturn();

        $column = $this->createColumnFromDataProviderEntry($column);
        $column->setDataTable($this->createDataTableMock());

        $columnHeaderView = $column->createHeaderView($this->createHeaderRowView());
        $columnHeaderView->getDataTable()->vars['themes'] = [$theme];

        if ($options['sort'] ?? false) {
            $columnHeaderView->getDataTable()->vars['sorting_enabled'] = true;
        }

        $actualHtml = ($twig = $this->getTwig())
            ->getExtension(DataTableExtension::class)
            ->renderColumnHeader($twig, $columnHeaderView);

        $this->assertHtmlEquals($expectedHtml, $actualHtml);
    }

    /**
     * @dataProvider resolvedColumnValueProvider
     */
    public function testRenderingColumnValue(string $theme, array $column = [], string $expectedHtml = null): void
    {
        $data = $column['data'];

        $column = $this->createColumnFromDataProviderEntry($column);

        $rowData = new class {};
        $rowData->{$column->getName()} = $column['data'];

        $columnValueView = $column->createValueView($this->createValueRowView($rowData));
        $columnValueView->getDataTable()->vars['themes'] = [$theme];

        $actualHtml = ($twig = $this->getTwig())
            ->getExtension(DataTableExtension::class)
            ->renderColumnValue($twig, $columnValueView);

        $this->assertHtmlEquals($expectedHtml, $actualHtml);
    }

    protected function createColumnFromDataProviderEntry(array $column = []): ColumnInterface
    {
        $options = $column['options'];

        if (null !== $name = $column['name']) {
            return $this->createNamedColumn($name, $options);
        }

        return $this->createColumn($options);
    }

    private static function resolveProviderEntries(iterable $entries): iterable
    {
        foreach ($entries as $description => $entry) {
            yield $description => [
                'theme' => $entry['theme'] ?? static::THEME_BASE,
                'column' => ($entry['column'] ?? []) + [
                    'name' => null,
                    'data' => null,
                    'options' => [],
                ],
                'expectedHtml' => $entry['expectedHtml'] ?? '',
            ];
        }
    }
}

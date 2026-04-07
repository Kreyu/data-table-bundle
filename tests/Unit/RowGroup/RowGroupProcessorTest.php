<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\RowGroup;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\RowGroup\AggregationDefinition;
use Kreyu\Bundle\DataTableBundle\RowGroup\AggregationType;
use Kreyu\Bundle\DataTableBundle\RowGroup\RowGroupFooterView;
use Kreyu\Bundle\DataTableBundle\RowGroup\RowGroupHeaderView;
use Kreyu\Bundle\DataTableBundle\RowGroup\RowGroupingConfiguration;
use Kreyu\Bundle\DataTableBundle\RowGroup\RowGroupProcessor;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use PHPUnit\Framework\TestCase;

class RowGroupProcessorTest extends TestCase
{
    private RowGroupProcessor $processor;

    protected function setUp(): void
    {
        $this->processor = new RowGroupProcessor();
    }

    public function testNoGroupingFieldsYieldsOriginalRows(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: []);

        $rows = $this->createValueRows($view, [
            ['name' => 'Alice', 'office' => 'Tokyo'],
            ['name' => 'Bob', 'office' => 'London'],
        ]);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, []));

        $this->assertCount(2, $result);
        $this->assertInstanceOf(ValueRowView::class, $result[0]);
        $this->assertInstanceOf(ValueRowView::class, $result[1]);
    }

    public function testSingleLevelGrouping(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: ['[office]']);

        $data = [
            ['name' => 'Alice', 'office' => 'Tokyo'],
            ['name' => 'Bob', 'office' => 'Tokyo'],
            ['name' => 'Charlie', 'office' => 'London'],
            ['name' => 'Dave', 'office' => 'London'],
        ];
        $rows = $this->createValueRows($view, $data);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, ['col1' => true, 'col2' => true]));

        // Expected: Header(Tokyo), Alice, Bob, Header(London), Charlie, Dave
        $this->assertCount(6, $result);
        $this->assertInstanceOf(RowGroupHeaderView::class, $result[0]);
        $this->assertSame('Tokyo', $result[0]->groupValue);
        $this->assertSame(0, $result[0]->level);
        $this->assertSame(2, $result[0]->columnCount);
        $this->assertInstanceOf(ValueRowView::class, $result[1]);
        $this->assertInstanceOf(ValueRowView::class, $result[2]);
        $this->assertInstanceOf(RowGroupHeaderView::class, $result[3]);
        $this->assertSame('London', $result[3]->groupValue);
        $this->assertInstanceOf(ValueRowView::class, $result[4]);
        $this->assertInstanceOf(ValueRowView::class, $result[5]);
    }

    public function testSingleLevelWithFooter(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: ['[office]']);
        $config->setEndRenderer(fn (mixed $value, string $field, int $level, array $aggs, int $rowCount) => [
            'summary' => "$value: $rowCount rows",
        ]);

        $data = [
            ['name' => 'Alice', 'office' => 'Tokyo'],
            ['name' => 'Bob', 'office' => 'Tokyo'],
            ['name' => 'Charlie', 'office' => 'London'],
        ];
        $rows = $this->createValueRows($view, $data);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, []));

        // Expected: Header(Tokyo), Alice, Bob, Footer(Tokyo), Header(London), Charlie, Footer(London)
        $this->assertCount(7, $result);
        $this->assertInstanceOf(RowGroupHeaderView::class, $result[0]);
        $this->assertInstanceOf(ValueRowView::class, $result[1]);
        $this->assertInstanceOf(ValueRowView::class, $result[2]);
        $this->assertInstanceOf(RowGroupFooterView::class, $result[3]);
        $this->assertSame('Tokyo', $result[3]->groupValue);
        $this->assertSame(2, $result[3]->rowCount);
        $this->assertSame('Tokyo: 2 rows', $result[3]->vars['summary']);
        $this->assertInstanceOf(RowGroupHeaderView::class, $result[4]);
        $this->assertInstanceOf(ValueRowView::class, $result[5]);
        $this->assertInstanceOf(RowGroupFooterView::class, $result[6]);
        $this->assertSame(1, $result[6]->rowCount);
    }

    public function testSingleLevelWithAggregations(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(
            fields: ['[office]'],
            aggregations: [
                new AggregationDefinition('salary', AggregationType::Sum, 'Total Salary', '[salary]'),
                new AggregationDefinition('salary', AggregationType::Count, 'Employees', '[salary]'),
            ],
        );
        $config->setEndRenderer(fn () => []);

        $data = [
            ['name' => 'Alice', 'office' => 'Tokyo', 'salary' => 5000],
            ['name' => 'Bob', 'office' => 'Tokyo', 'salary' => 6000],
            ['name' => 'Charlie', 'office' => 'London', 'salary' => 7000],
        ];
        $rows = $this->createValueRows($view, $data);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, []));

        // Find footer views
        $footers = array_filter($result, fn ($r) => $r instanceof RowGroupFooterView);
        $footers = array_values($footers);

        $this->assertCount(2, $footers);

        // Tokyo footer
        $tokyoAggs = $footers[0]->aggregations;
        $this->assertCount(2, $tokyoAggs);
        $this->assertSame(11000.0, $tokyoAggs[0]->getValue());
        $this->assertSame('Total Salary', $tokyoAggs[0]->getLabel());
        $this->assertSame(2, $tokyoAggs[1]->getValue());
        $this->assertSame('Employees', $tokyoAggs[1]->getLabel());

        // London footer
        $londonAggs = $footers[1]->aggregations;
        $this->assertSame(7000.0, $londonAggs[0]->getValue());
        $this->assertSame(1, $londonAggs[1]->getValue());
    }

    public function testMultiLevelGrouping(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: ['[country]', '[city]']);

        $data = [
            ['name' => 'Alice', 'country' => 'Japan', 'city' => 'Tokyo'],
            ['name' => 'Bob', 'country' => 'Japan', 'city' => 'Tokyo'],
            ['name' => 'Charlie', 'country' => 'Japan', 'city' => 'Osaka'],
            ['name' => 'Dave', 'country' => 'UK', 'city' => 'London'],
        ];
        $rows = $this->createValueRows($view, $data);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, []));

        // Expected:
        // Header(Japan, level=0), Header(Tokyo, level=1), Alice, Bob,
        // Header(Osaka, level=1), Charlie,
        // Header(UK, level=0), Header(London, level=1), Dave
        $headers = array_filter($result, fn ($r) => $r instanceof RowGroupHeaderView);
        $headers = array_values($headers);

        $this->assertCount(5, $headers);
        $this->assertSame('Japan', $headers[0]->groupValue);
        $this->assertSame(0, $headers[0]->level);
        $this->assertSame('Tokyo', $headers[1]->groupValue);
        $this->assertSame(1, $headers[1]->level);
        $this->assertSame('Osaka', $headers[2]->groupValue);
        $this->assertSame(1, $headers[2]->level);
        $this->assertSame('UK', $headers[3]->groupValue);
        $this->assertSame(0, $headers[3]->level);
        $this->assertSame('London', $headers[4]->groupValue);
        $this->assertSame(1, $headers[4]->level);
    }

    public function testMultiLevelGroupingWithFooters(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: ['[country]', '[city]']);
        $config->setEndRenderer(fn () => []);

        $data = [
            ['name' => 'Alice', 'country' => 'Japan', 'city' => 'Tokyo'],
            ['name' => 'Bob', 'country' => 'Japan', 'city' => 'Osaka'],
            ['name' => 'Charlie', 'country' => 'UK', 'city' => 'London'],
        ];
        $rows = $this->createValueRows($view, $data);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, []));

        $footers = array_values(array_filter($result, fn ($r) => $r instanceof RowGroupFooterView));

        // Footers: Tokyo(1 row), Osaka(1 row), Japan(2 rows), London(1 row), UK(1 row)
        $this->assertCount(5, $footers);
    }

    public function testCustomStartRenderer(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: ['[office]']);
        $config->setStartRenderer(fn (mixed $value) => [
            'custom_label' => "Group: $value",
        ]);

        $data = [
            ['name' => 'Alice', 'office' => 'Tokyo'],
        ];
        $rows = $this->createValueRows($view, $data);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, []));

        $this->assertInstanceOf(RowGroupHeaderView::class, $result[0]);
        $this->assertSame('Group: Tokyo', $result[0]->vars['custom_label']);
    }

    public function testGroupHeaderViewVars(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: ['[office]']);

        $data = [
            ['name' => 'Alice', 'office' => 'Tokyo'],
        ];
        $rows = $this->createValueRows($view, $data);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, ['a' => 1, 'b' => 2]));

        $header = $result[0];
        $this->assertInstanceOf(RowGroupHeaderView::class, $header);
        $this->assertSame('Tokyo', $header->vars['group_value']);
        $this->assertSame('[office]', $header->vars['field_name']);
        $this->assertSame(0, $header->vars['level']);
        $this->assertSame(2, $header->vars['column_count']);
        $this->assertSame('group_header', $header->vars['row_type']);
    }

    public function testGroupFooterViewVars(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: ['[office]']);
        $config->setEndRenderer(fn () => []);

        $data = [
            ['name' => 'Alice', 'office' => 'Tokyo'],
            ['name' => 'Bob', 'office' => 'Tokyo'],
        ];
        $rows = $this->createValueRows($view, $data);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, ['a' => 1]));

        $footers = array_values(array_filter($result, fn ($r) => $r instanceof RowGroupFooterView));
        $this->assertCount(1, $footers);

        $footer = $footers[0];
        $this->assertSame('Tokyo', $footer->vars['group_value']);
        $this->assertSame(2, $footer->vars['row_count']);
        $this->assertSame('group_footer', $footer->vars['row_type']);
    }

    public function testSingleRowGroup(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: ['[office]']);

        $data = [
            ['name' => 'Alice', 'office' => 'Tokyo'],
        ];
        $rows = $this->createValueRows($view, $data);

        $result = iterator_to_array($this->processor->process($rows, $config, $view, []));

        $this->assertCount(2, $result);
        $this->assertInstanceOf(RowGroupHeaderView::class, $result[0]);
        $this->assertInstanceOf(ValueRowView::class, $result[1]);
    }

    public function testEmptyDataset(): void
    {
        $view = new DataTableView();
        $config = new RowGroupingConfiguration(fields: ['[office]']);

        $result = iterator_to_array($this->processor->process([], $config, $view, []));

        $this->assertCount(0, $result);
    }

    /**
     * @return list<ValueRowView>
     */
    private function createValueRows(DataTableView $view, array $datasets): array
    {
        $rows = [];
        foreach ($datasets as $index => $data) {
            $rows[] = new ValueRowView($view, $index, $data);
        }

        return $rows;
    }
}

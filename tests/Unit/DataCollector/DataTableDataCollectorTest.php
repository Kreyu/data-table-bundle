<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\DataCollector;

use Kreyu\Bundle\DataTableBundle\Action\ActionConfigInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollector;
use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataExtractorInterface;
use Kreyu\Bundle\DataTableBundle\DataTableConfigInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationData;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationInterface;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingColumnData;
use Kreyu\Bundle\DataTableBundle\Sorting\SortingData;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\VarDumper\Cloner\Data;

class DataTableDataCollectorTest extends TestCase
{
    private MockObject&DataTableDataExtractorInterface $dataExtractor;
    private DataTableDataCollector $collector;

    protected function setUp(): void
    {
        $this->dataExtractor = $this->createMock(DataTableDataExtractorInterface::class);
        $this->collector = new DataTableDataCollector($this->dataExtractor);
    }

    public function testCollectDoesNothing(): void
    {
        $this->collector->collect(new Request(), new Response());

        $this->assertSame([], $this->collector->getData());
    }

    public function testGetTemplate(): void
    {
        $this->assertSame(
            '@KreyuDataTable/data_collector/template.html.twig',
            DataTableDataCollector::getTemplate(),
        );
    }

    public function testCollectDataTable(): void
    {
        $column = $this->createColumnMock('id');
        $filter = $this->createFilterMock('name');
        $action = $this->createActionMock('edit');
        $rowAction = $this->createActionMock('view');
        $batchAction = $this->createActionMock('delete');
        $exporter = $this->createExporterMock('csv');

        $config = $this->createMock(DataTableConfigInterface::class);
        $config->method('isPaginationEnabled')->willReturn(true);

        $dataTable = $this->createDataTableMock('users');
        $dataTable->method('getColumns')->willReturn(['id' => $column]);
        $dataTable->method('getFilters')->willReturn(['name' => $filter]);
        $dataTable->method('getActions')->willReturn(['edit' => $action]);
        $dataTable->method('getRowActions')->willReturn(['view' => $rowAction]);
        $dataTable->method('getBatchActions')->willReturn(['delete' => $batchAction]);
        $dataTable->method('getExporters')->willReturn(['csv' => $exporter]);
        $dataTable->method('getConfig')->willReturn($config);

        $this->dataExtractor->method('extractDataTableConfiguration')->willReturn(['type_class' => 'App\\DataTable']);
        $this->dataExtractor->method('extractColumnConfiguration')->willReturn(['column_config' => true]);
        $this->dataExtractor->method('extractFilterConfiguration')->willReturn(['filter_config' => true]);
        $this->dataExtractor
            ->method('extractActionConfiguration')
            ->willReturnCallback(
                static fn (ActionInterface $a) => ['action_name' => $a->getName()],
            )
        ;
        $this->dataExtractor->method('extractExporterConfiguration')->willReturn(['exporter_config' => true]);

        $this->collector->collectDataTable($dataTable);

        $this->assertArrayHasKey('users', $this->getCollectedArray());
        $this->assertSame('App\\DataTable', $this->getCollectedArray('users')['type_class']);
        $this->assertSame(['column_config' => true], $this->getCollectedArray('users', 'columns')['id']);
        $this->assertSame(['filter_config' => true], $this->getCollectedArray('users', 'filters')['name']);
        $this->assertSame(['action_name' => 'edit'], $this->getCollectedArray('users', 'actions')['edit']);
        $this->assertSame(['action_name' => 'view'], $this->getCollectedArray('users', 'row_actions')['view']);
        $this->assertSame(['action_name' => 'delete'], $this->getCollectedArray('users', 'batch_actions')['delete']);
        $this->assertSame(['exporter_config' => true], $this->getCollectedArray('users', 'exporters')['csv']);
    }

    public function testCollectDataTableWithPaginationDisabledCollectsTotalCount(): void
    {
        $config = $this->createMock(DataTableConfigInterface::class);
        $config->method('isPaginationEnabled')->willReturn(false);

        $dataTable = $this->createDataTableMock('products');
        $dataTable->method('getColumns')->willReturn([]);
        $dataTable->method('getFilters')->willReturn([]);
        $dataTable->method('getActions')->willReturn([]);
        $dataTable->method('getRowActions')->willReturn([]);
        $dataTable->method('getBatchActions')->willReturn([]);
        $dataTable->method('getExporters')->willReturn([]);
        $dataTable->method('getConfig')->willReturn($config);
        $dataTable->method('getItems')->willReturn(new \ArrayIterator([1, 2, 3]));

        $this->dataExtractor->method('extractDataTableConfiguration')->willReturn([]);

        $this->collector->collectDataTable($dataTable);

        $this->assertSame(3, $this->getCollectedArray('products')['total_count']);
    }

    public function testCollectDataTableView(): void
    {
        $this->initializeCollectorWithDataTable('orders');

        $view = new DataTableView();
        $view->vars = ['foo' => 'bar', 'baz' => 'qux'];

        $dataTable = $this->createDataTableMock('orders');
        $this->dataExtractor->method('extractValueRows')->willReturn([['row1'], ['row2']]);

        $this->collector->collectDataTableView($dataTable, $view);

        $ordersData = $this->getCollectedArray('orders');
        $this->assertSame(['baz' => 'qux', 'foo' => 'bar'], $ordersData['view_vars']);
        $this->assertSame([['row1'], ['row2']], $ordersData['value_rows']);
    }

    public function testCollectColumnHeaderView(): void
    {
        $this->initializeCollectorWithDataTable('users', columns: ['email']);

        $column = $this->createColumnMock('email');
        $columnDataTable = $this->createDataTableMock('users');
        $column->method('getDataTable')->willReturn($columnDataTable);

        $headerView = new ColumnHeaderView(new HeaderRowView(new DataTableView()));
        $headerView->vars = ['label' => 'Email', 'attr' => ['class' => 'email']];

        $this->collector->collectColumnHeaderView($column, $headerView);

        $this->assertSame(
            ['attr' => ['class' => 'email'], 'label' => 'Email'],
            $this->getCollectedArray('users', 'columns', 'email')['header_view_vars'],
        );
    }

    public function testCollectColumnValueViewSkipsNestedColumns(): void
    {
        $this->initializeCollectorWithDataTable('users', columns: ['tags']);

        $column = $this->createColumnMock('tags');
        $columnDataTable = $this->createDataTableMock('users');
        $column->method('getDataTable')->willReturn($columnDataTable);

        $dataTableView = new DataTableView();
        $parentRow = new ValueRowView($dataTableView, 0, ['id' => 1]);
        $parentRow->origin = $parentRow;

        $valueView = new ColumnValueView($parentRow);
        $valueView->vars = ['value' => 'test'];

        $this->collector->collectColumnValueView($column, $valueView);

        $this->assertArrayNotHasKey('value_view_vars', $this->getCollectedArray('users', 'columns', 'tags'));
    }

    public function testCollectColumnValueViewCollectsTopLevelColumns(): void
    {
        $this->initializeCollectorWithDataTable('users', columns: ['name']);

        $column = $this->createColumnMock('name');
        $columnDataTable = $this->createDataTableMock('users');
        $column->method('getDataTable')->willReturn($columnDataTable);

        $dataTableView = new DataTableView();
        $parentRow = new ValueRowView($dataTableView, 0, ['id' => 1]);

        $valueView = new ColumnValueView($parentRow);
        $valueView->vars = ['value' => 'John', 'attr' => []];

        $this->collector->collectColumnValueView($column, $valueView);

        $this->assertSame(
            ['attr' => [], 'value' => 'John'],
            $this->getCollectedArray('users', 'columns', 'name')['value_view_vars'],
        );
    }

    public function testCollectSortingData(): void
    {
        $this->initializeCollectorWithDataTable('users', columns: ['name', 'email']);

        $dataTable = $this->createDataTableMock('users');
        $dataTable
            ->method('hasColumn')
            ->willReturnCallback(static fn (string $name) => in_array($name, ['name', 'email'], strict: true))
        ;

        $nameColumn = $this->createColumnMock('name');
        $nameDataTable = $this->createDataTableMock('users');
        $nameColumn->method('getDataTable')->willReturn($nameDataTable);

        $emailColumn = $this->createColumnMock('email');
        $emailDataTable = $this->createDataTableMock('users');
        $emailColumn->method('getDataTable')->willReturn($emailDataTable);

        $dataTable
            ->method('getColumn')
            ->willReturnMap([
                ['name', $nameColumn],
                ['email', $emailColumn],
            ])
        ;

        $sortingData = new SortingData([
            new SortingColumnData('name', 'asc'),
            new SortingColumnData('email', 'desc'),
        ]);

        $this->collector->collectSortingData($dataTable, $sortingData);

        $this->assertSame('asc', $this->getCollectedArray('users', 'columns', 'name')['sort_direction']);
        $this->assertSame('desc', $this->getCollectedArray('users', 'columns', 'email')['sort_direction']);
    }

    public function testCollectSortingDataSkipsUnknownColumns(): void
    {
        $this->initializeCollectorWithDataTable('users');

        $dataTable = $this->createDataTableMock('users');
        $dataTable->method('hasColumn')->willReturn(false);

        $sortingData = new SortingData([
            new SortingColumnData('nonexistent', 'asc'),
        ]);

        $this->collector->collectSortingData($dataTable, $sortingData);

        $this->assertArrayNotHasKey('nonexistent', $this->getCollectedArray('users', 'columns'));
    }

    public function testCollectPaginationData(): void
    {
        $this->initializeCollectorWithDataTable('users');

        $pagination = $this->createMock(PaginationInterface::class);
        $pagination->method('getTotalItemCount')->willReturn(100);

        $dataTable = $this->createDataTableMock('users');
        $dataTable->method('getPagination')->willReturn($pagination);

        $paginationData = new PaginationData(3, 25);

        $this->collector->collectPaginationData($dataTable, $paginationData);

        $usersData = $this->getCollectedArray('users');
        $this->assertSame(3, $usersData['page']);
        $this->assertSame(25, $usersData['per_page']);
        $this->assertSame(100, $usersData['total_count']);
    }

    public function testCollectFilterView(): void
    {
        $this->initializeCollectorWithDataTable('users', filters: ['status']);

        $filter = $this->createFilterMock('status');
        $filterDataTable = $this->createDataTableMock('users');
        $filter->method('getDataTable')->willReturn($filterDataTable);

        $filterView = new FilterView(new DataTableView());
        $filterView->vars = ['label' => 'Status', 'attr' => []];

        $this->collector->collectFilterView($filter, $filterView);

        $this->assertSame(
            ['attr' => [], 'label' => 'Status'],
            $this->getCollectedArray('users', 'filters', 'status')['view_vars'],
        );
    }

    public function testCollectFiltrationData(): void
    {
        $this->initializeCollectorWithDataTable('users', filters: ['status', 'role']);

        $dataTable = $this->createDataTableMock('users');
        $dataTable
            ->method('hasFilter')
            ->willReturnCallback(static fn (string $name) => in_array($name, ['status', 'role'], strict: true))
        ;

        $statusFilter = $this->createFilterMock('status');
        $statusDataTable = $this->createDataTableMock('users');
        $statusFilter->method('getDataTable')->willReturn($statusDataTable);

        $roleFilter = $this->createFilterMock('role');
        $roleDataTable = $this->createDataTableMock('users');
        $roleFilter->method('getDataTable')->willReturn($roleDataTable);

        $dataTable
            ->method('getFilter')
            ->willReturnMap([
                ['status', $statusFilter],
                ['role', $roleFilter],
            ])
        ;

        $statusFilterData = new FilterData('active', Operator::Equals);
        $roleFilterData = new FilterData('admin');

        $filtrationData = new FiltrationData([
            'status' => $statusFilterData,
            'role' => $roleFilterData,
        ]);

        $this->collector->collectFiltrationData($dataTable, $filtrationData);

        $statusData = $this->getCollectedArray('users', 'filters', 'status');
        $this->assertSame($statusFilterData, $statusData['data']);
        $this->assertSame('Equals', $statusData['operator_label']);

        $roleData = $this->getCollectedArray('users', 'filters', 'role');
        $this->assertSame($roleFilterData, $roleData['data']);
        $this->assertNull($roleData['operator_label']);
    }

    public function testCollectFiltrationDataSkipsUnknownFilters(): void
    {
        $this->initializeCollectorWithDataTable('users');

        $dataTable = $this->createDataTableMock('users');
        $dataTable->method('hasFilter')->willReturn(false);

        $filtrationData = new FiltrationData([
            'nonexistent' => new FilterData('value'),
        ]);

        $this->collector->collectFiltrationData($dataTable, $filtrationData);

        $this->assertArrayNotHasKey('nonexistent', $this->getCollectedArray('users', 'filters'));
    }

    public function testCollectActionViewForGlobalAction(): void
    {
        $this->initializeCollectorWithDataTable('users');

        $this->assertActionViewCollected(ActionContext::Global, 'actions');
    }

    public function testCollectActionViewForRowAction(): void
    {
        $this->initializeCollectorWithDataTable('users');

        $this->assertActionViewCollected(ActionContext::Row, 'row_actions');
    }

    public function testCollectActionViewForBatchAction(): void
    {
        $this->initializeCollectorWithDataTable('users');

        $this->assertActionViewCollected(ActionContext::Batch, 'batch_actions');
    }

    public function testCollectDataTableMergesWithExistingData(): void
    {
        $config = $this->createMock(DataTableConfigInterface::class);
        $config->method('isPaginationEnabled')->willReturn(true);

        $dataTable = $this->createDataTableMock('users');
        $dataTable->method('getColumns')->willReturn([]);
        $dataTable->method('getFilters')->willReturn([]);
        $dataTable->method('getActions')->willReturn([]);
        $dataTable->method('getRowActions')->willReturn([]);
        $dataTable->method('getBatchActions')->willReturn([]);
        $dataTable->method('getExporters')->willReturn([]);
        $dataTable->method('getConfig')->willReturn($config);

        $this->dataExtractor->method('extractDataTableConfiguration')->willReturn(['type_class' => 'App\\DataTable']);

        $this->collector->collectDataTable($dataTable);
        $this->collector->collectDataTable($dataTable);

        $this->assertArrayHasKey('users', $this->getCollectedArray());
        $this->assertSame('App\\DataTable', $this->getCollectedArray('users')['type_class']);
    }

    public function testReset(): void
    {
        $this->initializeCollectorWithDataTable('users');

        $this->assertNotEmpty($this->collector->getData());

        $this->collector->reset();

        $this->assertSame([], $this->collector->getData());
    }

    public function testDataSurvivesSerializationRoundtrip(): void
    {
        $this->initializeCollectorWithDataTable('users', columns: ['name']);

        $column = $this->createColumnMock('name');
        $columnDataTable = $this->createDataTableMock('users');
        $column->method('getDataTable')->willReturn($columnDataTable);

        $headerView = new ColumnHeaderView(new HeaderRowView(new DataTableView()));
        $headerView->vars = ['label' => 'Name', 'attr' => []];
        $this->collector->collectColumnHeaderView($column, $headerView);

        /** @var DataTableDataCollector $restoredCollector */
        $restoredCollector = unserialize(serialize($this->collector));

        $this->assertInstanceOf(DataTableDataCollector::class, $restoredCollector);
        $this->assertInstanceOf(Data::class, $restoredCollector->getData());
    }

    private function assertActionViewCollected(ActionContext $context, string $dataKey): void
    {
        $actionConfig = $this->createMock(ActionConfigInterface::class);
        $actionConfig->method('getContext')->willReturn($context);

        $action = $this->createActionMock('test_action');
        $actionDataTable = $this->createDataTableMock('users');
        $action->method('getDataTable')->willReturn($actionDataTable);
        $action->method('getConfig')->willReturn($actionConfig);

        $actionView = new ActionView(new DataTableView());
        $actionView->vars = ['label' => 'Test', 'attr' => []];

        $this->collector->collectActionView($action, $actionView);

        $this->assertSame(
            ['attr' => [], 'label' => 'Test'],
            $this->getCollectedArray('users', $dataKey, 'test_action')['view_vars'],
        );
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getCollectedArray(string ...$keys): array
    {
        $data = $this->collector->getData();
        $this->assertIsArray($data);

        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $data);
            $this->assertIsArray($data[$key]);
            $data = $data[$key];
        }

        return $data;
    }

    /**
     * @param list<string> $columns
     * @param list<string> $filters
     */
    private function initializeCollectorWithDataTable(
        string $name,
        array $columns = [],
        array $filters = [],
    ): void {
        $config = $this->createMock(DataTableConfigInterface::class);
        $config->method('isPaginationEnabled')->willReturn(true);

        $columnMocks = [];
        foreach ($columns as $colName) {
            $columnMocks[$colName] = $this->createColumnMock($colName);
        }

        $filterMocks = [];
        foreach ($filters as $filterName) {
            $filterMocks[$filterName] = $this->createFilterMock($filterName);
        }

        $dataTable = $this->createDataTableMock($name);
        $dataTable->method('getColumns')->willReturn($columnMocks);
        $dataTable->method('getFilters')->willReturn($filterMocks);
        $dataTable->method('getActions')->willReturn([]);
        $dataTable->method('getRowActions')->willReturn([]);
        $dataTable->method('getBatchActions')->willReturn([]);
        $dataTable->method('getExporters')->willReturn([]);
        $dataTable->method('getConfig')->willReturn($config);

        $this->dataExtractor->method('extractDataTableConfiguration')->willReturn([]);
        $this->dataExtractor->method('extractColumnConfiguration')->willReturn([]);
        $this->dataExtractor->method('extractFilterConfiguration')->willReturn([]);
        $this->dataExtractor->method('extractActionConfiguration')->willReturn([]);
        $this->dataExtractor->method('extractExporterConfiguration')->willReturn([]);

        $this->collector->collectDataTable($dataTable);
    }

    private function createDataTableMock(string $name): MockObject&DataTableInterface
    {
        $dataTable = $this->createMock(DataTableInterface::class);
        $dataTable->method('getName')->willReturn($name);

        return $dataTable;
    }

    private function createColumnMock(string $name): MockObject&ColumnInterface
    {
        $column = $this->createMock(ColumnInterface::class);
        $column->method('getName')->willReturn($name);

        return $column;
    }

    private function createFilterMock(string $name): MockObject&FilterInterface
    {
        $filter = $this->createMock(FilterInterface::class);
        $filter->method('getName')->willReturn($name);

        return $filter;
    }

    private function createActionMock(string $name): MockObject&ActionInterface
    {
        $action = $this->createMock(ActionInterface::class);
        $action->method('getName')->willReturn($name);

        return $action;
    }

    private function createExporterMock(string $name): MockObject&ExporterInterface
    {
        $exporter = $this->createMock(ExporterInterface::class);
        $exporter->method('getName')->willReturn($name);

        return $exporter;
    }
}

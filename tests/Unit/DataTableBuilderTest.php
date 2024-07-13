<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactory;
use Kreyu\Bundle\DataTableBundle\Action\ActionRegistry;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
use Kreyu\Bundle\DataTableBundle\Column\Type\ActionsColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\CheckboxColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactory;
use Kreyu\Bundle\DataTableBundle\Column\Type\TextColumnType;
use Kreyu\Bundle\DataTableBundle\DataTableBuilder;
use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactory;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterRegistry;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterType;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Filter\FilterBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactory;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Exporter\Type\SimpleExporterType;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\SimpleFilterType;
use Kreyu\Bundle\DataTableBundle\Tests\ReflectionTrait;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DataTableBuilderTest extends TestCase
{
    use ReflectionTrait;

    public function testCloning()
    {
        $builder = $this->createBuilder();

        $query = $builder->getQuery();

        $clonedBuilder = clone $builder;

        $this->assertNotSame($query, $clonedBuilder->getQuery());
    }

    public function testGetQuery()
    {
        $query = $this->createStub(ProxyQueryInterface::class);

        $builder = $this->createBuilder();
        $builder->setQuery($query);

        $this->assertSame($query, $builder->getQuery());
    }

    public function testGetColumns()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());

        $builder->addColumn('foo');
        $builder->addColumn('bar', NumberColumnType::class);

        $columns = $builder->getColumns();

        $this->assertCount(2, $columns);
        $this->assertContainsOnlyInstancesOf(ColumnBuilderInterface::class, $columns);

        $this->assertSame('foo', $columns['foo']->getName());
        $this->assertSame('bar', $columns['bar']->getName());

        $this->assertInstanceOf(TextColumnType::class, $columns['foo']->getColumnConfig()->getType()->getInnerType());
        $this->assertInstanceOf(NumberColumnType::class, $columns['bar']->getColumnConfig()->getType()->getInnerType());
    }

    public function testGetColumnNonExistent()
    {
        $this->expectException(InvalidArgumentException::class);

        $builder = $this->createBuilder();
        $builder->getColumn('foo');
    }

    public function testGetColumn()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());
        $builder->addColumn('foo');

        $column = $builder->getColumn('foo');

        $this->assertInstanceOf(ColumnBuilderInterface::class, $column);
        $this->assertSame('foo', $column->getName());
        $this->assertInstanceOf(TextColumnType::class, $column->getColumnConfig()->getType()->getInnerType());
    }

    public function testCreateColumn()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());

        $column = $builder->createColumn('foo');

        $this->assertInstanceOf(ColumnBuilderInterface::class, $column);
        $this->assertSame('foo', $column->getName());
        $this->assertSame(TextColumnType::class, $column->getType()->getInnerType()::class);
    }

    public function testAddColumnWithColumnBuilder()
    {
        $columnFactory = $this->createColumnFactory();

        $column = $columnFactory->createNamedBuilder('foo');

        $builder = $this->createBuilder();
        $builder->addColumn($column);

        $this->assertSame($column, $builder->getColumn('foo'));
    }

    public function testHasColumn()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());

        $this->assertFalse($builder->hasColumn('foo'));

        $builder->addColumn('foo');

        $this->assertTrue($builder->hasColumn('foo'));
    }

    public function testRemoveColumn()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());

        $builder->addColumn('foo');
        $builder->removeColumn('foo');

        $this->assertFalse($builder->hasColumn('foo'));
    }

    public function testGetFilters()
    {
        $builder = $this->createBuilder();
        $builder->setFilterFactory($this->createFilterFactory());

        $builder->addFilter('foo');
        $builder->addFilter('bar', SimpleFilterType::class);

        $filters = $builder->getFilters();

        $this->assertCount(2, $filters);
        $this->assertContainsOnlyInstancesOf(FilterBuilderInterface::class, $filters);

        $this->assertSame('foo', $filters['foo']->getName());
        $this->assertSame('bar', $filters['bar']->getName());

        $this->assertInstanceOf(FilterType::class, $filters['foo']->getFilterConfig()->getType()->getInnerType());
        $this->assertInstanceOf(SimpleFilterType::class, $filters['bar']->getFilterConfig()->getType()->getInnerType());
    }

    public function testGetFilterNonExistent()
    {
        $this->expectException(InvalidArgumentException::class);

        $builder = $this->createBuilder();
        $builder->getFilter('foo');
    }

    public function testGetFilter()
    {
        $builder = $this->createBuilder();
        $builder->setFilterFactory($this->createFilterFactory());
        $builder->addFilter('foo');

        $filter = $builder->getFilter('foo');

        $this->assertInstanceOf(FilterBuilderInterface::class, $filter);
        $this->assertSame('foo', $filter->getName());
        $this->assertInstanceOf(FilterType::class, $filter->getFilterConfig()->getType()->getInnerType());
    }

    public function testCreateFilter()
    {
        $builder = $this->createBuilder();
        $builder->setFilterFactory($this->createFilterFactory());

        $filter = $builder->createFilter('foo');

        $this->assertInstanceOf(FilterBuilderInterface::class, $filter);
        $this->assertSame('foo', $filter->getName());
        $this->assertSame(FilterType::class, $filter->getType()->getInnerType()::class);
    }

    public function testAddFilterWithFilterBuilder()
    {
        $filterFactory = $this->createFilterFactory();

        $filter = $filterFactory->createNamedBuilder('foo');

        $builder = $this->createBuilder();
        $builder->addFilter($filter);

        $this->assertSame($filter, $builder->getFilter('foo'));
    }

    public function testHasFilter()
    {
        $builder = $this->createBuilder();
        $builder->setFilterFactory($this->createFilterFactory());

        $this->assertFalse($builder->hasFilter('foo'));

        $builder->addFilter('foo');

        $this->assertTrue($builder->hasFilter('foo'));
    }

    public function testRemoveFilter()
    {
        $builder = $this->createBuilder();
        $builder->setFilterFactory($this->createFilterFactory());

        $builder->addFilter('foo');
        $builder->removeFilter('foo');

        $this->assertFalse($builder->hasFilter('foo'));
    }

    public function testGetSearchHandler()
    {
        $handler = fn () => null;

        $builder = $this->createBuilder();
        $builder->setSearchHandler($handler);

        $this->assertSame($handler, $builder->getSearchHandler());
    }

    public function testSearchHandlerAddsSearchFilter()
    {
        $builder = $this->createBuilder();
        $builder->setFilterFactory($this->createFilterFactory());
        $builder->setSearchHandler(fn () => null);

        $dataTable = $builder->getDataTable();

        $this->assertCount(1, $dataTable->getFilters());
        $this->assertTrue($dataTable->hasFilter('__search'));
        $this->assertInstanceOf(SearchFilterType::class, $dataTable->getFilter('__search')->getConfig()->getType()->getInnerType());
    }

    public function testSearchHandlerWithAutoAddingSearchFilterDisabled()
    {
        $builder = $this->createBuilder();
        $builder->setFilterFactory($this->createFilterFactory());
        $builder->setAutoAddingSearchFilter(false);
        $builder->setSearchHandler(fn () => null);

        $dataTable = $builder->getDataTable();

        $this->assertEmpty($dataTable->getFilters());
    }

    public function testSearchHandlerWithSearchFilterAlreadyDefined()
    {
        $builder = $this->createBuilder();
        $builder->setFilterFactory($this->createFilterFactory());
        $builder->setSearchHandler(fn () => null);
        $builder->addFilter('__search', SimpleFilterType::class);

        $dataTable = $builder->getDataTable();

        $this->assertInstanceOf(SimpleFilterType::class, $dataTable->getFilter('__search')->getConfig()->getType()->getInnerType());
    }

    public function testGetActions()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $builder->addAction('foo');
        $builder->addAction('bar', LinkActionType::class);

        $actions = $builder->getActions();

        $this->assertCount(2, $actions);
        $this->assertContainsOnlyInstancesOf(ActionBuilderInterface::class, $actions);

        $this->assertSame('foo', $actions['foo']->getName());
        $this->assertSame('bar', $actions['bar']->getName());

        $this->assertInstanceOf(ButtonActionType::class, $actions['foo']->getActionConfig()->getType()->getInnerType());
        $this->assertInstanceOf(LinkActionType::class, $actions['bar']->getActionConfig()->getType()->getInnerType());
    }

    public function testGetActionNonExistent()
    {
        $this->expectException(InvalidArgumentException::class);

        $builder = $this->createBuilder();
        $builder->getAction('foo');
    }

    public function testGetAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());
        $builder->addAction('foo');

        $action = $builder->getAction('foo');

        $this->assertInstanceOf(ActionBuilderInterface::class, $action);
        $this->assertSame('foo', $action->getName());
        $this->assertInstanceOf(ButtonActionType::class, $action->getActionConfig()->getType()->getInnerType());
    }

    public function testCreateAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $action = $builder->createAction('foo');

        $this->assertInstanceOf(ActionBuilderInterface::class, $action);
        $this->assertSame('foo', $action->getName());
        $this->assertSame(ButtonActionType::class, $action->getType()->getInnerType()::class);
        $this->assertSame(ActionContext::Global, $action->getContext());
    }

    public function testAddActionWithActionBuilder()
    {
        $actionFactory = $this->createActionFactory();

        $action = $actionFactory->createNamedBuilder('foo');

        $builder = $this->createBuilder();
        $builder->addAction($action);

        $this->assertSame($action, $builder->getAction('foo'));
    }

    public function testHasAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $this->assertFalse($builder->hasAction('foo'));

        $builder->addAction('foo');

        $this->assertTrue($builder->hasAction('foo'));
    }

    public function testRemoveAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $builder->addAction('foo');
        $builder->removeAction('foo');

        $this->assertFalse($builder->hasAction('foo'));
    }

    public function testGetBatchAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());
        $builder->addBatchAction('foo');

        $action = $builder->getBatchAction('foo');

        $this->assertInstanceOf(ActionBuilderInterface::class, $action);
        $this->assertSame('foo', $action->getName());
        $this->assertInstanceOf(ButtonActionType::class, $action->getActionConfig()->getType()->getInnerType());
    }

    public function testCreateBatchAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $action = $builder->createBatchAction('foo');

        $this->assertInstanceOf(ActionBuilderInterface::class, $action);
        $this->assertSame('foo', $action->getName());
        $this->assertSame(ButtonActionType::class, $action->getType()->getInnerType()::class);
        $this->assertSame(ActionContext::Batch, $action->getContext());
    }

    public function testAddBatchActionWithBatchActionBuilder()
    {
        $actionFactory = $this->createActionFactory();

        $action = $actionFactory->createNamedBuilder('foo');

        $builder = $this->createBuilder();
        $builder->addBatchAction($action);

        $this->assertSame($action, $builder->getBatchAction('foo'));
    }

    public function testHasBatchAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $this->assertFalse($builder->hasBatchAction('foo'));

        $builder->addBatchAction('foo');

        $this->assertTrue($builder->hasBatchAction('foo'));
    }

    public function testRemoveBatchAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $builder->addBatchAction('foo');
        $builder->removeBatchAction('foo');

        $this->assertFalse($builder->hasBatchAction('foo'));
    }

    public function testBatchActionAddsCheckboxColumn()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());
        $builder->setActionFactory($this->createActionFactory());
        $builder->addBatchAction('foo');

        $dataTable = $builder->getDataTable();

        $this->assertCount(1, $dataTable->getColumns());
        $this->assertTrue($dataTable->hasColumn('__batch'));
        $this->assertInstanceOf(CheckboxColumnType::class, $dataTable->getColumn('__batch')->getConfig()->getType()->getInnerType());
    }

    public function testBatchActionWithAutoAddingBatchCheckboxColumnDisabled()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());
        $builder->setAutoAddingBatchCheckboxColumn(false);
        $builder->addBatchAction('foo');

        $dataTable = $builder->getDataTable();

        $this->assertEmpty($dataTable->getColumns());
    }

    public function testBatchActionWithBatchCheckboxColumnAlreadyDefined()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());
        $builder->setActionFactory($this->createActionFactory());
        $builder->addBatchAction('foo');
        $builder->addColumn('__batch', TextColumnType::class);

        $dataTable = $builder->getDataTable();

        $this->assertInstanceOf(TextColumnType::class, $dataTable->getColumn('__batch')->getConfig()->getType()->getInnerType());
    }

    public function testGetRowAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());
        $builder->addRowAction('foo');

        $action = $builder->getRowAction('foo');

        $this->assertInstanceOf(ActionBuilderInterface::class, $action);
        $this->assertSame('foo', $action->getName());
        $this->assertInstanceOf(ButtonActionType::class, $action->getActionConfig()->getType()->getInnerType());
    }

    public function testCreateRowAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $action = $builder->createRowAction('foo');

        $this->assertInstanceOf(ActionBuilderInterface::class, $action);
        $this->assertSame('foo', $action->getName());
        $this->assertSame(ButtonActionType::class, $action->getType()->getInnerType()::class);
        $this->assertSame(ActionContext::Row, $action->getContext());
    }

    public function testAddRowActionWithRowActionBuilder()
    {
        $actionFactory = $this->createActionFactory();

        $action = $actionFactory->createNamedBuilder('foo');

        $builder = $this->createBuilder();
        $builder->addRowAction($action);

        $this->assertSame($action, $builder->getRowAction('foo'));
    }

    public function testHasRowAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $this->assertFalse($builder->hasRowAction('foo'));

        $builder->addRowAction('foo');

        $this->assertTrue($builder->hasRowAction('foo'));
    }

    public function testRemoveRowAction()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());

        $builder->addRowAction('foo');
        $builder->removeRowAction('foo');

        $this->assertFalse($builder->hasRowAction('foo'));
    }

    public function testRowActionAddsActionsColumn()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());
        $builder->setActionFactory($this->createActionFactory());
        $builder->addRowAction('foo');

        $dataTable = $builder->getDataTable();

        $this->assertCount(1, $dataTable->getColumns());
        $this->assertTrue($dataTable->hasColumn('__actions'));
        $this->assertInstanceOf(ActionsColumnType::class, $dataTable->getColumn('__actions')->getConfig()->getType()->getInnerType());
        $this->assertSame($builder->getRowActions(), $dataTable->getColumn('__actions')->getConfig()->getOption('actions'));
    }

    public function testRowActionWithAutoAddingActionsColumnDisabled()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());
        $builder->setActionFactory($this->createActionFactory());
        $builder->setAutoAddingActionsColumn(false);
        $builder->addRowAction('foo');

        $dataTable = $builder->getDataTable();

        $this->assertEmpty($dataTable->getColumns());
    }

    public function testRowActionWithActionsColumnAlreadyDefined()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());
        $builder->setActionFactory($this->createActionFactory());
        $builder->addRowAction('foo');
        $builder->addColumn('__actions', TextColumnType::class);

        $dataTable = $builder->getDataTable();

        $this->assertInstanceOf(TextColumnType::class, $dataTable->getColumn('__actions')->getConfig()->getType()->getInnerType());
    }

    public function testGetExporters()
    {
        $builder = $this->createBuilder();
        $builder->setExporterFactory($this->createExporterFactory());

        $builder->addExporter('foo');
        $builder->addExporter('bar', SimpleExporterType::class);

        $exporters = $builder->getExporters();

        $this->assertCount(2, $exporters);
        $this->assertContainsOnlyInstancesOf(ExporterBuilderInterface::class, $exporters);

        $this->assertSame('foo', $exporters['foo']->getName());
        $this->assertSame('bar', $exporters['bar']->getName());

        $this->assertInstanceOf(ExporterType::class, $exporters['foo']->getExporterConfig()->getType()->getInnerType());
        $this->assertInstanceOf(SimpleExporterType::class, $exporters['bar']->getExporterConfig()->getType()->getInnerType());
    }

    public function testGetExporterNonExistent()
    {
        $this->expectException(InvalidArgumentException::class);

        $builder = $this->createBuilder();
        $builder->getExporter('foo');
    }

    public function testGetExporter()
    {
        $builder = $this->createBuilder();
        $builder->setExporterFactory($this->createExporterFactory());
        $builder->addExporter('foo');

        $action = $builder->getExporter('foo');

        $this->assertInstanceOf(ExporterBuilderInterface::class, $action);
        $this->assertSame('foo', $action->getName());
        $this->assertInstanceOf(ExporterType::class, $action->getExporterConfig()->getType()->getInnerType());
    }

    public function testCreateExporterAction()
    {
        $builder = $this->createBuilder();
        $builder->setExporterFactory($this->createExporterFactory());

        $exporter = $builder->createExporter('foo');

        $this->assertInstanceOf(ExporterBuilderInterface::class, $exporter);
        $this->assertSame('foo', $exporter->getName());
        $this->assertSame(ExporterType::class, $exporter->getType()->getInnerType()::class);
    }

    public function testAddExporterWithExporterBuilder()
    {
        $exporterFactory = $this->createExporterFactory();

        $exporter = $exporterFactory->createNamedBuilder('foo');

        $builder = $this->createBuilder();
        $builder->addExporter($exporter);

        $this->assertSame($exporter, $builder->getExporter('foo'));
    }

    public function testHasExporter()
    {
        $builder = $this->createBuilder();
        $builder->setExporterFactory($this->createExporterFactory());

        $this->assertFalse($builder->hasExporter('foo'));

        $builder->addExporter('foo');

        $this->assertTrue($builder->hasExporter('foo'));
    }

    public function testRemoveExporter()
    {
        $builder = $this->createBuilder();
        $builder->setExporterFactory($this->createExporterFactory());

        $builder->addExporter('foo');
        $builder->removeExporter('foo');

        $this->assertFalse($builder->hasExporter('foo'));
    }

    public function testGetDataTableResolvesColumns()
    {
        $builder = $this->createBuilder();
        $builder->setColumnFactory($this->createColumnFactory());
        $builder->addColumn('foo');
        $builder->addColumn('bar', NumberColumnType::class);

        $dataTable = $builder->getDataTable();

        $expectedColumns = array_map(function (ColumnBuilderInterface $columnBuilder) use ($dataTable) {
            return $columnBuilder->getColumn()->setDataTable($dataTable);
        }, $builder->getColumns());

        $this->assertEquals($expectedColumns, $dataTable->getColumns());
    }

    public function testGetDataTableResolvesFilters()
    {
        $builder = $this->createBuilder();
        $builder->setFilterFactory($this->createFilterFactory());
        $builder->addFilter('foo');
        $builder->addFilter('bar', SimpleFilterType::class);

        $dataTable = $builder->getDataTable();

        $expectedFilters = array_map(function (FilterBuilderInterface $columnBuilder) use ($dataTable) {
            return $columnBuilder->getFilter()->setDataTable($dataTable);
        }, $builder->getFilters());

        $this->assertEquals($expectedFilters, $dataTable->getFilters());
    }

    public function testGetDataTableResolvesActions()
    {
        $builder = $this->createBuilder();
        $builder->setActionFactory($this->createActionFactory());
        $builder->addAction('foo');
        $builder->addAction('bar', LinkActionType::class);

        $dataTable = $builder->getDataTable();

        $expectedActions = array_map(function (ActionBuilderInterface $columnBuilder) use ($dataTable) {
            return $columnBuilder->getAction()->setDataTable($dataTable);
        }, $builder->getActions());

        $this->assertEquals($expectedActions, $dataTable->getActions());
    }

    public function testGetDataTableResolvesExporters()
    {
        $builder = $this->createBuilder();
        $builder->setExporterFactory($this->createExporterFactory());
        $builder->addExporter('foo');
        $builder->addExporter('bar', SimpleExporterType::class);

        $dataTable = $builder->getDataTable();

        $expectedExporters = array_map(function (ExporterBuilderInterface $columnBuilder) use ($dataTable) {
            return $columnBuilder->getExporter()->setDataTable($dataTable);
        }, $builder->getExporters());

        $this->assertEquals($expectedExporters, $dataTable->getExporters());
    }

    public function testGetDataTableInitializesDataTable()
    {
        $dataTable = $this->createBuilder()->getDataTable();

        $this->assertTrue($this->getPrivatePropertyValue($dataTable, 'initialized'));
    }

    private function createBuilder(): DataTableBuilder
    {
        return new DataTableBuilder(
            name: 'foo',
            type: $this->createStub(ResolvedDataTableTypeInterface::class),
            query: $this->createStub(ProxyQueryInterface::class),
            dispatcher: $this->createStub(EventDispatcherInterface::class),
            options: [],
        );
    }

    private function createColumnFactory(): ColumnFactory
    {
        return new ColumnFactory(
            new ColumnRegistry(
                types: [
                    new ColumnType(),
                    new TextColumnType(),
                    new NumberColumnType(),
                    new CheckboxColumnType(),
                    new ActionsColumnType($this->createActionFactory()),
                ],
                typeExtensions: [],
                resolvedTypeFactory: new ResolvedColumnTypeFactory(),
            ),
        );
    }

    private function createFilterFactory(): FilterFactory
    {
        return new FilterFactory(
            new FilterRegistry(
                types: [
                    new FilterType(),
                    new SimpleFilterType(),
                    new SearchFilterType(),
                ],
                typeExtensions: [],
                resolvedTypeFactory: new ResolvedFilterTypeFactory(),
            ),
        );
    }

    private function createActionFactory(): ActionFactory
    {
        return new ActionFactory(
            new ActionRegistry(
                types: [
                    new ActionType(),
                    new LinkActionType(),
                    new ButtonActionType(),
                ],
                typeExtensions: [],
                resolvedTypeFactory: new ResolvedActionTypeFactory(),
            ),
        );
    }

    private function createExporterFactory(): ExporterFactory
    {
        return new ExporterFactory(
            new ExporterRegistry(
                types: [
                    new ExporterType(),
                    new SimpleExporterType(),
                ],
                typeExtensions: [],
                resolvedTypeFactory: new ResolvedExporterTypeFactory(),
            ),
        );
    }
}

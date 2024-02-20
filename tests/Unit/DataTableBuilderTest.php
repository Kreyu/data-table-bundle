<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactory;
use Kreyu\Bundle\DataTableBundle\Action\ActionRegistry;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ButtonActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\LinkActionType;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactory;
use Kreyu\Bundle\DataTableBundle\Column\ColumnRegistry;
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

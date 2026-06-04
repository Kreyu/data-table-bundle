<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Type;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\RowIterator;
use Kreyu\Bundle\DataTableBundle\Test\DataTableIntegrationTestCase;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query\CountingProxyQuery;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class DataTableTypeTest extends DataTableIntegrationTestCase
{
    public function testAsyncDeferredRenderSkipsQuery(): void
    {
        $dataTable = $this->createCountingDataTable(['async' => true]);

        $view = $dataTable->createView();

        $this->assertSame(0, CountingProxyQuery::$getResultCalls, 'No query may run on a deferred render.');
        $this->assertTrue($view->vars['is_async']);
        $this->assertTrue($view->vars['async_deferred']);
        $this->assertNull($view->pagination);
        $this->assertSame([], $view->valueRows);
        $this->assertSame([], $view->vars['url_query_parameters']);
    }

    public function testAsyncFrameRequestRendersData(): void
    {
        $dataTable = $this->createCountingDataTable(['async' => true], [['id' => 1], ['id' => 2]]);
        $dataTable->setTurboFrameId('kreyu_data_table_'.$dataTable->getConfig()->getName());

        $view = $dataTable->createView();

        $this->assertSame(1, CountingProxyQuery::$getResultCalls, 'The frame request must execute the query.');
        $this->assertFalse($view->vars['async_deferred']);
        $this->assertNotNull($view->pagination);
        $this->assertInstanceOf(RowIterator::class, $view->valueRows);
    }

    public function testNonAsyncTableIsUnaffected(): void
    {
        $dataTable = $this->createCountingDataTable([], [['id' => 1]]);

        $view = $dataTable->createView();

        $this->assertFalse($view->vars['is_async']);
        $this->assertFalse($view->vars['async_deferred']);
        $this->assertNotNull($view->pagination);
        $this->assertInstanceOf(RowIterator::class, $view->valueRows);
    }

    public function testAsyncLoadingOptionPropagatesToView(): void
    {
        $eager = $this->createCountingDataTable(['async' => true, 'async_loading' => 'eager'])->createView();
        $this->assertSame('eager', $eager->vars['async_loading']);

        $default = $this->createCountingDataTable(['async' => true])->createView();
        $this->assertSame('lazy', $default->vars['async_loading']);
    }

    public function testInvalidAsyncOptionIsRejected(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->dataTableFactory->createBuilder(DataTableType::class, [], ['async' => 'yes']);
    }

    public function testInvalidAsyncLoadingOptionIsRejected(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->dataTableFactory->createBuilder(DataTableType::class, [], ['async_loading' => 'always']);
    }

    private function createCountingDataTable(array $options, array $data = []): DataTableInterface
    {
        CountingProxyQuery::reset();

        $builder = $this->dataTableFactory->createBuilder(DataTableType::class, [], $options);
        $builder->setQuery(new CountingProxyQuery($data));
        $builder->addColumn('id');

        return $builder->getDataTable();
    }
}

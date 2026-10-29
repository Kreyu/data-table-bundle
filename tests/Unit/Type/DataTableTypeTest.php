<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Type;

use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactory;
use Kreyu\Bundle\DataTableBundle\Filter\FilterRegistry;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Type\CallbackFilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterType;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactory;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterType;
use Kreyu\Bundle\DataTableBundle\Pagination\Pagination;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\RowIterator;
use Kreyu\Bundle\DataTableBundle\Test\DataTableIntegrationTestCase;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query\CountingProxyQuery;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;

class DataTableTypeTest extends DataTableIntegrationTestCase
{
    public function testAsyncDeferredRenderSkipsQuery(): void
    {
        $dataTable = $this->createCountingDataTable([
            'async' => true,
            'filtration_enabled' => true,
            'personalization_enabled' => true,
            'exporting_enabled' => true,
        ]);

        $view = $dataTable->createView();

        $this->assertSame(0, CountingProxyQuery::$getResultCalls, 'No query may run on a deferred render.');
        $this->assertTrue($view->vars['is_async']);
        $this->assertTrue($view->vars['async_deferred']);
        $this->assertNull($view->pagination);
        $this->assertSame([], $view->valueRows);
        $this->assertSame([], $view->vars['url_query_parameters']);
        $this->assertArrayNotHasKey('filtration_form', $view->vars);
        $this->assertArrayNotHasKey('personalization_form', $view->vars);
        $this->assertArrayNotHasKey('export_form', $view->vars);
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

    public function testCustomPaginationFactoryIsUsed(): void
    {
        $customPagination = new Pagination(
            currentPageNumber: 1,
            currentPageItemCount: 1,
            totalItemCount: 1,
        );

        $paginationFactory = $this->createStub(PaginationFactoryInterface::class);
        $paginationFactory->method('create')->willReturn($customPagination);

        $dataTable = $this->createCountingDataTable(['pagination_factory' => $paginationFactory], [['id' => 1]]);

        $this->assertSame($customPagination, $dataTable->getPagination());
    }

    public function testUrlQueryParametersKeepFilters(): void
    {
        $options = [
            'filtration_enabled' => true,
            'filter_factory' => $this->createFilterFactory(),
            'filtration_form_factory' => Forms::createFormFactory(),
        ];

        $builder = $this->dataTableFactory->createBuilder(DataTableType::class, [['id' => 1]], $options);
        $builder->addColumn('id');
        $builder->addFilter('author.name', CallbackFilterType::class, ['callback' => static fn () => null]);
        $builder->setSearchHandler(static fn () => null);

        $dataTable = $builder->getDataTable();
        $dataTable->filter(FiltrationData::fromArray([
            DataTableBuilderInterface::SEARCH_FILTER_NAME => 'foo',
            'author.name' => 'bar',
        ]));

        /** @var array<string, array<string, array{value: mixed}>> $urlQueryParameters */
        $urlQueryParameters = $dataTable->createView()->vars['url_query_parameters'];
        $filtrationParameterName = $dataTable->getConfig()->getFiltrationParameterName();

        $expectedFiltrationParameters = [
            'author:name' => ['value' => 'bar'],
            DataTableBuilderInterface::SEARCH_FILTER_NAME => ['value' => 'foo'],
        ];

        $this->assertSame($expectedFiltrationParameters, $urlQueryParameters[$filtrationParameterName]);
    }

    public function testPersonalizationFormSupportsDottedColumnNames(): void
    {
        $options = [
            'personalization_enabled' => true,
            'personalization_form_factory' => Forms::createFormFactory(),
        ];

        $builder = $this->dataTableFactory->createBuilder(DataTableType::class, [['id' => 1]], $options);
        $builder->addColumn('id', options: ['priority' => 2]);
        $builder->addColumn('author.name', options: ['priority' => 1]);

        $dataTable = $builder->getDataTable();

        /** @var FormView $personalizationForm */
        $personalizationForm = $dataTable->createView()->vars['personalization_form'];

        $this->assertSame(['id', 'author:name'], array_keys($personalizationForm['columns']->children));
        $this->assertSame('Author.name', $personalizationForm['columns']['author:name']->vars['label']);

        $form = $dataTable->createPersonalizationFormBuilder()->getForm();
        $form->submit([
            'columns' => [
                'id' => ['name' => 'id', 'priority' => '1', 'visible' => '0'],
                'author:name' => ['name' => 'author.name', 'priority' => '2', 'visible' => '1'],
            ],
        ]);

        /** @var PersonalizationData $submittedData */
        $submittedData = $form->getData();

        $this->assertTrue($form->isValid());

        $dataTable->personalize($submittedData);

        $this->assertSame(['id', 'author.name'], array_keys($submittedData->getColumns()));
        $this->assertSame(['author.name', 'id'], array_keys($dataTable->getColumns()));
        $this->assertSame(['author.name'], array_keys($dataTable->getVisibleColumns()));
        $this->assertSame(['id'], array_keys($dataTable->getHiddenColumns()));
    }

    private function createCountingDataTable(array $options, array $data = []): DataTableInterface
    {
        CountingProxyQuery::reset();

        $builder = $this->dataTableFactory->createBuilder(DataTableType::class, [], $options);
        $builder->setQuery(new CountingProxyQuery($data));
        $builder->addColumn('id');

        return $builder->getDataTable();
    }

    private function createFilterFactory(): FilterFactory
    {
        $registry = new FilterRegistry(
            types: [
                new FilterType(),
                new CallbackFilterType(),
                new SearchFilterType(),
            ],
            typeExtensions: [],
            resolvedTypeFactory: new ResolvedFilterTypeFactory(),
        );

        return new FilterFactory($registry);
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterClearUrlGenerator;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FilterClearUrlGeneratorTest extends TestCase
{
    private const ROUTE_NAME = 'users_index';
    private const DATA_TABLE_NAME = 'users';
    private const PAGE_PARAMETER_NAME = 'page_'.self::DATA_TABLE_NAME;
    private const FILTRATION_PARAMETER_NAME = 'filter_'.self::DATA_TABLE_NAME;

    private MockObject&Request $request;
    private MockObject&RequestStack $requestStack;
    private MockObject&UrlGeneratorInterface $urlGenerator;

    protected function setUp(): void
    {
        $this->request = $this->createMock(Request::class);
        $this->request->attributes = new ParameterBag(['_route' => self::ROUTE_NAME]);
        $this->request->query = new InputBag();

        $this->requestStack = $this->createMock(RequestStack::class);
        $this->requestStack->method('getCurrentRequest')->willReturn($this->request);

        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->urlGenerator->method('generate')->willReturn('');
    }

    public function testItPreservesRouteParams()
    {
        $this->request->attributes->set('_route_params', ['id' => 1]);

        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            'id' => 1,
        ]);

        $this->generate();
    }

    public function testItPreservesQueryParams()
    {
        $this->request->query->set('action', 'list');

        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            'action' => 'list',
        ]);

        $this->generate();
    }

    public function testItPreservesDataTableUrlQueryParameters()
    {
        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            'foo' => 'bar',
        ]);

        $this->generate($this->createDataTableViewMock(['foo' => 'bar']));
    }

    public function testItIncludesEmptyFilterParameters()
    {
        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::FILTRATION_PARAMETER_NAME => [
                'firstName' => [
                    'value' => '',
                    'operator' => null,
                ],
                'lastName' => [
                    'value' => '',
                ],
            ],
        ]);

        $this->generate(
            $this->createDataTableViewMock(),
            $this->createFilterViewMock('firstName', operatorSelectable: true),
            $this->createFilterViewMock('lastName', operatorSelectable: false),
        );
    }

    public function testItOverridesCurrentPageNumberToFirst()
    {
        $this->request->query->set(self::PAGE_PARAMETER_NAME, 3);

        $dataTableView = $this->createDataTableViewMock([self::PAGE_PARAMETER_NAME => 2]);
        $dataTableView->vars['pagination_enabled'] = true;

        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::PAGE_PARAMETER_NAME => 1,
        ]);

        $this->generate($dataTableView);
    }

    public function testItMergesEverythingTogether(): void
    {
        $this->request->attributes->set('_route_params', ['id' => 1]);
        $this->request->query->set('action', 'list');

        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            'id' => 1,
            'action' => 'list',
            'foo' => 'bar',
            self::PAGE_PARAMETER_NAME => 1,
            self::FILTRATION_PARAMETER_NAME => [
                'firstName' => [
                    'value' => '',
                    'operator' => null,
                ],
                'lastName' => [
                    'value' => '',
                ],
            ],
        ]);

        $dataTableView = $this->createDataTableViewMock(['foo' => 'bar']);
        $dataTableView->vars['pagination_enabled'] = true;

        $this->generate(
            $dataTableView,
            $this->createFilterViewMock('firstName', operatorSelectable: true),
            $this->createFilterViewMock('lastName', operatorSelectable: false),
        );
    }

    private function generate(?DataTableView $dataTableView = null, FilterView ...$filterViews): void
    {
        $dataTableView ??= $this->createMock(DataTableView::class);

        $filterClearUrlGenerator = new FilterClearUrlGenerator($this->requestStack, $this->urlGenerator);
        $filterClearUrlGenerator->generate($dataTableView, ...$filterViews);
    }

    private function createDataTableViewMock(array $urlQueryParameters = []): DataTableView
    {
        $dataTableView = $this->createMock(DataTableView::class);
        $dataTableView->vars['filtration_parameter_name'] = self::FILTRATION_PARAMETER_NAME;
        $dataTableView->vars['page_parameter_name'] = self::PAGE_PARAMETER_NAME;
        $dataTableView->vars['url_query_parameters'] = $urlQueryParameters;

        return $dataTableView;
    }

    private function createFilterViewMock(string $name, bool $operatorSelectable): MockObject&FilterView
    {
        $filterView = $this->createMock(FilterView::class);
        $filterView->vars['name'] = $name;
        $filterView->vars['operator_selectable'] = $operatorSelectable;
        $filterView->vars['is_header_filter'] = true;

        $filterView->parent = $this->createDataTableViewMock();

        return $filterView;
    }
}

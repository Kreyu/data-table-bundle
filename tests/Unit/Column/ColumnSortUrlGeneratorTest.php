<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Column;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnSortUrlGenerator;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ColumnSortUrlGeneratorTest extends TestCase
{
    private const ROUTE_NAME = 'users_index';
    private const DATA_TABLE_NAME = 'users';
    private const PAGE_PARAMETER_NAME = 'page_'.self::DATA_TABLE_NAME;
    private const SORT_PARAMETER_NAME = 'sort_'.self::DATA_TABLE_NAME;

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

    public function testItRemovesOtherSortParameters()
    {
        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::SORT_PARAMETER_NAME => [
                'firstName' => 'asc',
            ],
        ]);

        $this->generate(
            $this->createDataTableViewMock([self::SORT_PARAMETER_NAME => [
                'middleName' => 'asc',
                'lastName' => 'desc',
            ]]),
            $this->createColumnHeaderViewMock('firstName', null),
        );
    }

    public function testItGeneratesWithOppositeDirections(): void
    {
        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::SORT_PARAMETER_NAME => [
                'firstName' => 'asc',
                'middleName' => 'desc',
                'lastName' => 'asc',
            ],
        ]);

        $this->generate(
            $this->createDataTableViewMock(),
            $this->createColumnHeaderViewMock('firstName', null),
            $this->createColumnHeaderViewMock('middleName', 'asc'),
            $this->createColumnHeaderViewMock('lastName', 'desc'),
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
            self::SORT_PARAMETER_NAME => [
                'firstName' => 'asc',
                'middleName' => 'desc',
                'lastName' => 'asc',
            ],
        ]);

        $dataTableView = $this->createDataTableViewMock(['foo' => 'bar']);
        $dataTableView->vars['pagination_enabled'] = true;

        $this->generate(
            $dataTableView,
            $this->createColumnHeaderViewMock('firstName', null),
            $this->createColumnHeaderViewMock('middleName', 'asc'),
            $this->createColumnHeaderViewMock('lastName', 'desc'),
        );
    }

    private function generate(?DataTableView $dataTableView = null, ColumnHeaderView ...$columnHeaderViews): void
    {
        $dataTableView ??= $this->createDataTableViewMock();

        $columnSortUrlGenerator = new ColumnSortUrlGenerator($this->requestStack, $this->urlGenerator);
        $columnSortUrlGenerator->generate($dataTableView, ...$columnHeaderViews);
    }

    private function createDataTableViewMock(array $urlQueryParameters = []): DataTableView
    {
        $dataTableView = $this->createMock(DataTableView::class);
        $dataTableView->vars['sort_parameter_name'] = self::SORT_PARAMETER_NAME;
        $dataTableView->vars['page_parameter_name'] = self::PAGE_PARAMETER_NAME;
        $dataTableView->vars['url_query_parameters'] = $urlQueryParameters;

        return $dataTableView;
    }

    private function createColumnHeaderViewMock(string $name, ?string $direction): MockObject&ColumnHeaderView
    {
        $columnHeaderView = $this->createMock(ColumnHeaderView::class);
        $columnHeaderView->parent = $this->createMock(HeaderRowView::class);
        $columnHeaderView->parent->parent = $this->createDataTableViewMock();

        $columnHeaderView->vars['name'] = $name;
        $columnHeaderView->vars['sort_direction'] = $direction;

        return $columnHeaderView;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Unit\Pagination;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationUrlGenerator;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginationUrlGeneratorTest extends TestCase
{
    private const ROUTE_NAME = 'users_index';
    private const DATA_TABLE_NAME = 'users';
    private const PAGE_PARAMETER_NAME = 'page_'.self::DATA_TABLE_NAME;

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
            self::PAGE_PARAMETER_NAME => 1,
            'id' => 1,
        ]);

        $this->generate();
    }

    public function testItPreservesQueryParams()
    {
        $this->request->query->set('action', 'list');

        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::PAGE_PARAMETER_NAME => 1,
            'action' => 'list',
        ]);

        $this->generate();
    }

    public function testItPreservesDataTableUrlQueryParameters()
    {
        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::PAGE_PARAMETER_NAME => 1,
            'foo' => 'bar',
        ]);

        $this->generate($this->createPaginationViewMock(['foo' => 'bar']));
    }

    public function testItIncludesGivenPage()
    {
        $this->request->query->set(self::PAGE_PARAMETER_NAME, 3);

        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::PAGE_PARAMETER_NAME => 5,
        ]);

        $this->generate($this->createPaginationViewMock([self::PAGE_PARAMETER_NAME => 2]), page: 5);
    }

    public function testItMergesEverythingTogether(): void
    {
        $this->request->attributes->set('_route_params', ['id' => 1]);
        $this->request->query->set('action', 'list');

        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::PAGE_PARAMETER_NAME => 5,
            'id' => 1,
            'action' => 'list',
            'foo' => 'bar',
        ]);

        $this->generate($this->createPaginationViewMock(['foo' => 'bar']), page: 5);
    }

    private function generate(?PaginationView $paginationView = null, int $page = 1): void
    {
        $paginationView ??= $this->createPaginationViewMock();

        $paginationUrlGenerator = new PaginationUrlGenerator($this->requestStack, $this->urlGenerator);
        $paginationUrlGenerator->generate($paginationView, $page);
    }

    private function createDataTableViewMock(array $urlQueryParameters = []): MockObject&DataTableView
    {
        $dataTableView = $this->createMock(DataTableView::class);
        $dataTableView->vars['page_parameter_name'] = self::PAGE_PARAMETER_NAME;
        $dataTableView->vars['url_query_parameters'] = $urlQueryParameters;

        return $dataTableView;
    }

    private function createPaginationViewMock(array $urlQueryParameters = []): MockObject&PaginationView
    {
        $paginationView = $this->createMock(PaginationView::class);
        $paginationView->parent = $this->createDataTableViewMock($urlQueryParameters);

        return $paginationView;
    }
}

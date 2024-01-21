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
    }

    public function testItGeneratesUrlWithOppositeDirection(): void
    {
        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::DATA_TABLE_NAME => [
                'firstName' => 'asc',
                'middleName' => 'desc',
                'lastName' => 'asc',
            ],
        ]);

        $this->generate(
            $this->createColumnHeaderViewMock('firstName', null),
            $this->createColumnHeaderViewMock('middleName', 'asc'),
            $this->createColumnHeaderViewMock('lastName', 'desc'),
        );
    }

    public function testItGeneratesWithoutColumnHeaderViews(): void
    {
        $this->request->attributes->set('_route_params', ['id' => 1]);
        $this->request->query->set('action', 'list');

        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            'id' => 1,
            'action' => 'list',
        ]);

        $this->generate();
    }

    public function testItMergesWithRouteAndQueryParameters(): void
    {
        $this->request->attributes->set('_route_params', ['id' => 1]);
        $this->request->query->set('action', 'list');

        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::DATA_TABLE_NAME => [
                'firstName' => 'asc',
            ],
            'id' => 1,
            'action' => 'list',
        ]);

        $this->generate(
            $this->createColumnHeaderViewMock('firstName', null),
        );
    }

    private function generate(MockObject&ColumnHeaderView ...$columnHeaderViews): void
    {
        $columnSortUrlGenerator = new ColumnSortUrlGenerator($this->requestStack, $this->urlGenerator);
        $columnSortUrlGenerator->generate(...$columnHeaderViews);
    }

    private function createColumnHeaderViewMock(string $name, ?string $direction): MockObject&ColumnHeaderView
    {
        $columnHeaderView = $this->createMock(ColumnHeaderView::class);
        $columnHeaderView->parent = $this->createMock(HeaderRowView::class);
        $columnHeaderView->parent->parent = $this->createMock(DataTableView::class);
        $columnHeaderView->parent->parent->vars['sort_parameter_name'] = self::DATA_TABLE_NAME;

        $columnHeaderView->vars['name'] = $name;
        $columnHeaderView->vars['sort_direction'] = $direction;

        return $columnHeaderView;
    }
}

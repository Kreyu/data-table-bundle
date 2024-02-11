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

    public function testItGenerates(): void
    {
        $this->urlGenerator->expects($this->once())->method('generate')->with(self::ROUTE_NAME, [
            self::DATA_TABLE_NAME => [
                'firstName' => [
                    'value' => '',
                    'operator' => null,
                ],
                'middleName' => [
                    'value' => '',
                    'operator' => null,
                ],
                'lastName' => [
                    'value' => '',
                    'operator' => null,
                ],
            ],
        ]);

        $this->generate(
            $this->createFilterViewMock('firstName'),
            $this->createFilterViewMock('middleName'),
            $this->createFilterViewMock('lastName'),
        );
    }

    public function testItGeneratesWithoutFilterViews(): void
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
                'firstName' => [
                    'value' => '',
                    'operator' => null,
                ],
            ],
            'id' => 1,
            'action' => 'list',
        ]);

        $this->generate(
            $this->createFilterViewMock('firstName'),
        );
    }

    private function generate(MockObject&FilterView ...$filterViews): void
    {
        $filterClearUrlGenerator = new FilterClearUrlGenerator($this->requestStack, $this->urlGenerator);
        $filterClearUrlGenerator->generate(...$filterViews);
    }

    private function createFilterViewMock(string $name): MockObject&FilterView
    {
        $filterView = $this->createMock(FilterView::class);
        $filterView->parent = $this->createMock(DataTableView::class);
        $filterView->parent->vars['filtration_parameter_name'] = self::DATA_TABLE_NAME;

        $filterView->vars['name'] = $name;

        return $filterView;
    }
}

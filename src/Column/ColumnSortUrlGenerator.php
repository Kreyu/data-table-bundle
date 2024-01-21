<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ColumnSortUrlGenerator implements ColumnSortUrlGeneratorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generate(ColumnHeaderView ...$columnHeaderViews): string
    {
        $request = $this->getRequest();

        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();

        $parameters = [...$routeParams, ...$queryParams];

        foreach ($columnHeaderViews as $columnHeaderView) {
            $parameters = array_replace_recursive($parameters, $this->getColumnSortQueryParameters($columnHeaderView));
        }

        return $this->urlGenerator->generate($route, $parameters);
    }

    private function getColumnSortQueryParameters(ColumnHeaderView $columnHeaderView): array
    {
        $dataTableView = $columnHeaderView->parent->parent;

        return [
            $dataTableView->vars['sort_parameter_name'] => [
                $columnHeaderView->vars['name'] => $this->getOppositeSortDirection($columnHeaderView),
            ],
        ];
    }

    private function getOppositeSortDirection(ColumnHeaderView $columnHeaderView): string
    {
        $sortDirection = mb_strtolower((string) $columnHeaderView->vars['sort_direction']);

        if ('asc' === $sortDirection) {
            return 'desc';
        }

        return 'asc';
    }

    private function getRequest(): Request
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            throw new LogicException('Unable to retrieve current request.');
        }

        return $request;
    }
}

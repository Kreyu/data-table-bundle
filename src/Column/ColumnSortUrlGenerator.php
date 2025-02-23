<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column;

use Kreyu\Bundle\DataTableBundle\DataTableView;
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

    public function generate(DataTableView $dataTableView, ColumnHeaderView ...$columnHeaderViews): string
    {
        $request = $this->getRequest();

        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();

        $parameters = [...$routeParams, ...$queryParams];

        // Recursively replace/merge with the URL query parameters defined in the data table view.
        // This allows the user to define custom query parameters that should be preserved when sorting columns.
        $parameters = array_replace_recursive($parameters, $dataTableView->vars['url_query_parameters'] ?? []);

        // Remove all sort-related parameters to only apply sorting on given columns.
        unset($parameters[$dataTableView->vars['sort_parameter_name']]);

        foreach ($columnHeaderViews as $columnHeaderView) {
            $parameters = array_replace_recursive($parameters, $this->getColumnSortQueryParameters($columnHeaderView));
        }

        // Clearing the filters should reset the pagination to the first page.
        if ($dataTableView->vars['pagination_enabled'] ?? false) {
            $parameters[$dataTableView->vars['page_parameter_name']] = 1;
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

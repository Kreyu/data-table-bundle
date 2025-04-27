<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FilterClearUrlGenerator implements FilterClearUrlGeneratorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generate(DataTableView $dataTableView, FilterView ...$filterViews): string
    {
        $request = $this->getRequest();

        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();

        $parameters = [...$routeParams, ...$queryParams];

        // Recursively replace/merge with the URL query parameters defined in the data table view.
        // This allows the user to define custom query parameters that should be preserved when clearing filters.
        $parameters = array_replace_recursive($parameters, $dataTableView->vars['url_query_parameters'] ?? []);

        foreach ($filterViews as $filterView) {
            $parameters = array_replace_recursive($parameters, $this->getFilterClearQueryParameters($filterView));
        }

        // Clearing the filters should reset the pagination to the first page.
        if ($dataTableView->vars['pagination_enabled'] ?? false) {
            $parameters[$dataTableView->vars['page_parameter_name']] = 1;
        }

        return $this->urlGenerator->generate($route, $parameters);
    }

    private function getFilterClearQueryParameters(FilterView $filterView): array
    {
        $value = $filterView->data?->getValue();
        if (is_array($value)) {
            $parameters = ['value' => array_map(fn () => '', $value)];
        } else {
            $parameters = ['value' => ''];
        }

        if ($filterView->vars['operator_selectable']) {
            $parameters['operator'] = null;
        }

        $dataTableView = $filterView->parent;

        return [
            $dataTableView->vars['filtration_parameter_name'] => [
                $filterView->vars['name'] => $parameters,
            ],
        ];
    }

    private function getRequest(): Request
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            throw new LogicException('Unable to retrieve current request.');
        }

        return $request;
    }
}

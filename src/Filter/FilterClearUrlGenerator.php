<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

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

    public function generate(FilterView ...$filterViews): string
    {
        $request = $this->getRequest();

        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();

        $parameters = [...$routeParams, ...$queryParams];

        foreach ($filterViews as $filterView) {
            $parameters = array_replace_recursive($parameters, $this->getFilterClearQueryParameters($filterView));
        }

        return $this->urlGenerator->generate($route, $parameters);
    }

    private function getFilterClearQueryParameters(FilterView $filterView): array
    {
        $dataTableView = $filterView->parent;

        return [
            $dataTableView->vars['filtration_parameter_name'] => [
                $filterView->vars['name'] => [
                    'value' => '',
                    'operator' => null,
                ],
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

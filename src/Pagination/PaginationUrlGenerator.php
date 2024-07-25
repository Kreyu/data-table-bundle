<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Pagination;

use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaginationUrlGenerator implements PaginationUrlGeneratorInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generate(PaginationView $paginationView, int $page): string
    {
        $request = $this->getRequest();

        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();

        $parameters = [...$routeParams, ...$queryParams];

        // Recursively replace/merge with the URL query parameters defined in the data table view.
        // This allows the user to define custom query parameters that should be preserved when changing pages.
        $parameters = array_replace_recursive($parameters, $paginationView->parent->vars['url_query_parameters'] ?? []);

        $parameters[$paginationView->parent->vars['page_parameter_name']] = $page;

        return $this->urlGenerator->generate($route, $parameters);
    }

    private function getRequest(): Request
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            throw new LogicException('Unable to retrieve current request.');
        }

        return $request;
    }
}

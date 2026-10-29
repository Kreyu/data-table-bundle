<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Request;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class AbstractUrlGenerator
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * @return array<array-key, mixed>
     */
    protected function getUrlParameters(DataTableView $dataTableView): array
    {
        $request = $this->getCurrentRequest();

        /** @var array<string, mixed> $routeParameters */
        $routeParameters = $request->attributes->get('_route_params', []);

        $queryParameters = $request->query->all();

        /** @var array<array-key, mixed> $urlQueryParameters */
        $urlQueryParameters = $dataTableView->vars['url_query_parameters'] ?? [];

        return array_replace_recursive([...$routeParameters, ...$queryParameters], $urlQueryParameters);
    }

    /**
     * @return array<string, int>
     */
    protected function getFirstPageParameters(DataTableView $dataTableView): array
    {
        /** @var bool $isPaginationEnabled */
        $isPaginationEnabled = $dataTableView->vars['pagination_enabled'] ?? false;

        if (!$isPaginationEnabled) {
            return [];
        }

        /** @var string $pageParameterName */
        $pageParameterName = $dataTableView->vars['page_parameter_name'];

        return [$pageParameterName => 1];
    }

    /**
     * @param array<array-key, mixed> $parameters
     */
    protected function generateUrl(array $parameters): string
    {
        $route = $this->getCurrentRequest()->attributes->getString('_route');

        if ($route === '') {
            throw new LogicException('Unable to generate URL, the current request has no "_route" attribute.');
        }

        return $this->urlGenerator->generate($route, $parameters);
    }

    private function getCurrentRequest(): Request
    {
        $request = $this->requestStack->getCurrentRequest();

        if ($request === null) {
            throw new LogicException('Unable to retrieve current request.');
        }

        return $request;
    }
}

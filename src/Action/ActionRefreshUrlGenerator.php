<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action;

use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ActionRefreshUrlGenerator implements ActionRefreshUrlGeneratorInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function generate(): string
    {
        $request = $this->getRequest();

        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();

        $parameters = [...$routeParams, ...$queryParams];

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

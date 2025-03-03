<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionContext;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RefreshActionType extends AbstractActionType
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
        private TranslatorInterface $translator,
    ) {
    }

    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        if (ActionContext::Global !== $action->getConfig()->getContext()) {
            throw new \LogicException(sprintf('A %s action can only be added as a global action.', $this::class));
        }

        $view->vars = array_replace($view->vars, [
            'href' => $this->generate(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => $this->translator->trans('Refresh', [], 'KreyuDataTable'),
        ]);
    }

    private function generate(): string
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            throw new UnexpectedTypeException($request, Request::class);
        }

        $route = $request->attributes->get('_route');
        $routeParams = $request->attributes->get('_route_params', []);
        $queryParams = $request->query->all();

        $parameters = [...$routeParams, ...$queryParams];

        return $this->urlGenerator->generate($route, $parameters);
    }
}

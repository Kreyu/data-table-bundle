<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;

final class ModalActionType extends AbstractActionType
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        if (null === $options['href'] && null === $options['route']) {
            throw new LogicException('No "route" or "href" provided.');
        }

        if ($view->parent instanceof ColumnValueView) {
            $value = $view->parent->value;

            foreach (['href', 'route', 'route_params'] as $optionName) {
                if (isset($options[$optionName]) && is_callable($options[$optionName])) {
                    $options[$optionName] = $options[$optionName]($value);
                }
            }
        } else {
            foreach (['href', 'route', 'route_params'] as $optionName) {
                if (isset($options[$optionName]) && is_callable($options[$optionName])) {
                    throw new LogicException(sprintf('Callable used for option "%s", but it\'s only available for RowActions.', $optionName));
                }
            }
        }

        $href = $options['href'] ?? $this->router->generate($options['route'], $options['route_params']);

        $view->vars = array_replace($view->vars, [
            'href' => $href,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('route')
            ->default(null)
            ->allowedTypes('null', 'string', 'callable')
        ;

        $resolver
            ->define('route_params')
            ->allowedTypes('array', 'callable')
            ->default([])
        ;

        $resolver
            ->define('href')
            ->default(null)
            ->allowedTypes('null', 'string', 'callable')
        ;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Exception\LogicException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ModalActionType extends AbstractActionType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
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
                if (isset($options[$optionName]) && $options[$optionName] instanceof \Closure) {
                    $options[$optionName] = $options[$optionName]($value);
                }
            }
        } else {
            foreach (['href', 'route', 'route_params'] as $optionName) {
                if (isset($options[$optionName]) && $options[$optionName] instanceof \Closure) {
                    throw new LogicException(sprintf('Closure used for option "%s", but it\'s only available for row actions.', $optionName));
                }
            }
        }

        $href = $options['href'] ?? $this->urlGenerator->generate($options['route'], $options['route_params']);

        $view->vars = array_replace($view->vars, [
            'href' => $href,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('route')
            ->default(null)
            ->allowedTypes('null', 'string', \Closure::class)
        ;

        $resolver
            ->define('route_params')
            ->allowedTypes('array', \Closure::class)
            ->default([])
        ;

        $resolver
            ->define('href')
            ->default(null)
            ->allowedTypes('null', 'string', \Closure::class)
        ;
    }
}

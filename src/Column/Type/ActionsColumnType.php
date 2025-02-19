<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ActionsColumnType extends AbstractColumnType
{
    public function __construct(
        private readonly ActionFactoryInterface $actionFactory,
    ) {
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $actions = [];

        foreach ($options['actions'] as $name => $action) {
            $action = $this->resolveAction($name, $action, $view);

            if (null === $action) {
                continue;
            }

            $action->setDataTable($column->getDataTable());

            $actions[$name] = $action->createView($view);
        }

        $view->vars['actions'] = array_filter($actions);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/actions#actions */
        $resolver->define('actions')
            ->default([])
            ->allowedTypes('actions', 'array[]', ActionBuilderInterface::class.'[]', ActionInterface::class.'[]')
            ->info('An array of actions to render in the column.')
            ->normalize(function (Options $options, mixed $value) {
                ($resolver = new OptionsResolver())
                    ->setRequired([
                        'type',
                    ])
                    ->setDefaults([
                        'type_options' => [],
                        'visible' => true,
                    ])
                    ->setAllowedTypes('type', ['string'])
                    ->setAllowedTypes('type_options', ['array', 'callable'])
                    ->setAllowedTypes('visible', ['bool', 'callable'])
                    ->setInfo('type', 'A fully-qualified class name of the action type.')
                    ->setInfo('type_options', 'An array of options passed to the action type.')
                    ->setInfo('visible', 'Determines whether the action should be visible.')
                ;

                foreach ($value as $name => $action) {
                    if (is_array($action)) {
                        $value[$name] = $resolver->resolve($action);
                    }
                }

                return $value;
            })
        ;

        $resolver->setDefaults([
            'export' => false,
            'property_path' => false,
        ]);
    }

    private function resolveAction(
        string $name,
        array|ActionBuilderInterface|ActionInterface $action,
        ColumnHeaderView|ColumnValueView $view,
    ): ?ActionInterface {
        if ($action instanceof ActionInterface) {
            return $action;
        }

        if ($action instanceof ActionBuilderInterface) {
            return $action->getAction();
        }

        $visible = $action['visible'];

        if ($view instanceof ColumnValueView && is_callable($visible)) {
            $visible = $visible($view->value);
        }

        if (!$visible) {
            return null;
        }

        return $this->actionFactory->createNamed($name, $action['type'], $action['type_options']);
    }
}

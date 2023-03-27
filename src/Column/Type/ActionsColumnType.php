<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionsColumnType extends AbstractColumnType
{
    public function __construct(
        private ActionFactoryInterface $actionFactory,
    ) {
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $actions = [];

        foreach ($options['actions'] as $name => $actionOptions) {
            $action = $this->actionFactory->create($name, $actionOptions['type'], $actionOptions['type_options']);
            $action->setData($column->getRowData());

            $actions[$name] = $action->createView();
        }

        $view->vars['actions'] = $actions;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'export' => false,
                'property_path' => false,
                'actions' => function (OptionsResolver $resolver) {
                    $resolver
                        ->setPrototype(true)
                        ->setRequired([
                            'type',
                        ])
                        ->setDefaults([
                            'type_options' => [],
                        ])
                        ->setAllowedTypes('type', ['string'])
                        ->setAllowedTypes('type_options', ['array', 'callable'])
                        ->setInfo('type', 'A fully-qualified class name of the action type.')
                        ->setInfo('type_options', 'An array of options passed to the action type.')
                    ;
                },
            ])
            ->setInfo('actions', 'An array of actions configuration, which contains of their type and options.')
        ;
    }
}

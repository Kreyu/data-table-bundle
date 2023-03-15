<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionsColumnType extends AbstractColumnType
{
    public function __construct(
        private ActionFactoryInterface $actionFactory,
    ) {
    }

    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
        $actions = [];

        foreach ($options['actions'] as $name => $actionOptions) {
            $action = $this->actionFactory->create($name, $actionOptions['type'], $actionOptions['type_options']);
            $action->setData($column->getData());

            $actions[$name] = $action->createView($view->parent);
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
                        ->setAllowedTypes('type_options', ['array', \Closure::class])
                    ;
                },
            ])
        ;
    }
}

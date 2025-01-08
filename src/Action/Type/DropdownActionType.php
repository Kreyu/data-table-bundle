<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DropdownActionType extends AbstractActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $itemActions = [];
        /** @var ActionBuilderInterface $itemActionBuilder */
        foreach ($options['actions'] as $itemActionBuilder) {
            $itemAction = $itemActionBuilder->getAction();
            $itemAction->setDataTable($action->getDataTable());

            $itemActions[] = $itemAction->createView($view->parent);
        }

        $view->vars['actions'] = $itemActions;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('actions')
            ->allowedTypes(ActionBuilderInterface::class.'[]')
            ->required()
        ;
    }
}

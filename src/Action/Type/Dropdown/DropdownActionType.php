<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type\Dropdown;

use Kreyu\Bundle\DataTableBundle\Action\ActionBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DropdownActionType extends AbstractActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $itemActions = [];

        if (is_callable($options['actions']) && $view->parent instanceof ColumnValueView) {
            $options['actions'] = $options['actions']($view->parent->value);
        }

        foreach ($options['actions'] as $itemActionBuilder) {
            if (!$itemActionBuilder instanceof ActionBuilderInterface) {
                throw new UnexpectedTypeException($itemActionBuilder, ActionBuilderInterface::class);
            }

            $itemActionBuilder->setContext($action->getConfig()->getContext());

            $itemAction = $itemActionBuilder->getAction();
            $itemAction->setDataTable($action->getDataTable());

            $itemActions[] = $itemAction->createView($view->parent);
        }

        $view->vars = array_replace($view->vars, [
            'actions' => $itemActions,
            'with_caret' => $options['with_caret'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('actions')
            ->allowedTypes(ActionBuilderInterface::class.'[]', 'callable')
            ->required()
            ->info('The actions to display in the dropdown.')
        ;

        $resolver->define('with_caret')
            ->default(true)
            ->allowedTypes('bool')
            ->info('Whether to display a caret next to the dropdown label.')
        ;
    }
}

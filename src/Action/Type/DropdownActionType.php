<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DropdownActionType extends AbstractActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $view->vars['actions'] = [];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('actions')
            ->allowedTypes('array')
            ->required()
        ;
    }
}

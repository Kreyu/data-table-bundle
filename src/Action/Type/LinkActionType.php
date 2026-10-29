<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LinkActionType extends AbstractActionType
{
    public function buildView(ActionView $view, ActionInterface $action, array $options): void
    {
        $columnValueView = $view->parent;

        if ($columnValueView instanceof ColumnValueView) {
            foreach (['href', 'target'] as $optionName) {
                if (!$options[$optionName] instanceof \Closure) {
                    continue;
                }

                $options[$optionName] = $options[$optionName]($columnValueView->value);
            }
        }

        $view->vars = array_replace($view->vars, [
            'href' => $options['href'],
            'target' => $options['target'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'href' => '#',
                'target' => null,
            ])
            ->setAllowedTypes('href', ['string', \Closure::class])
            ->setAllowedTypes('target', ['null', 'string', \Closure::class])
        ;
    }
}

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
        if ($view->parent instanceof ColumnValueView) {
            $value = $view->parent->value;

            foreach (['href', 'target'] as $optionName) {
                if (is_callable($options[$optionName])) {
                    $options[$optionName] = $options[$optionName]($value);
                }
            }
        }

        $view->vars = array_replace($view->vars, [
            'href' => $options['href'],
            'target' => $options['target'],
            'link_attr' => $options['link_attr'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'href' => '#',
                'target' => '_self',
                'link_attr' => [],
            ])
            ->setAllowedTypes('href', ['string', 'callable'])
            ->setAllowedTypes('target', ['string', 'callable'])
        ;
    }
}

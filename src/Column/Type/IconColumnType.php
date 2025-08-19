<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/icon
 */
final class IconColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        foreach (['icon', 'icon_attr'] as $optionName) {
            if ($options[$optionName] instanceof \Closure) {
                $options[$optionName] = $options[$optionName]($view->value);
            }
        }

        $view->vars = array_replace($view->vars, [
            'icon' => $options['icon'],
            'icon_attr' => $options['icon_attr'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('icon')
            ->required()
            ->allowedTypes('string', \Closure::class)
            ->info('Defines the icon to render.')
        ;

        $resolver->define('icon_attr')
            ->default([])
            ->allowedTypes('array', \Closure::class)
            ->info('Defines the HTML attributes for the icon to render.')
        ;
    }
}

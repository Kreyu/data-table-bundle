<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class IconColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        foreach (['icon', 'icon_attr'] as $optionName) {
            if (is_callable($options[$optionName])) {
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
        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/icon#icon */
        $resolver->define('icon')
            ->required()
            ->allowedTypes('string', 'callable')
            ->info('Defines the icon to render.')
        ;

        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/icon#icon_attr */
        $resolver->define('icon_attr')
            ->default([])
            ->allowedTypes('array', 'callable')
            ->info('Defines the HTML attributes for the icon to render.')
        ;
    }
}

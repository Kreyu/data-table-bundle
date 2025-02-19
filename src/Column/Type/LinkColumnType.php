<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LinkColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        if (is_callable($href = $options['href'])) {
            $href = $href($view->vars['data'], $view->parent->data, $column);
        }

        if (is_callable($target = $options['target'])) {
            $target = $target($view->vars['data'], $view->parent->data, $column);
        }

        $view->vars = array_replace($view->vars, [
            'href' => $href,
            'target' => $target,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/link#href */
        $resolver->define('href')
            ->default('#')
            ->allowedTypes('string', 'callable')
            ->info('Defines the URL to link to.')
        ;

        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/link#target */
        $resolver->define('target')
            ->default(null)
            ->allowedTypes('null', 'string', 'callable')
            ->info('Sets the value that will be used as a "target" HTML attribute.')
        ;
    }
}

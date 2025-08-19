<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a column with value displayed as a link.
 *
 * When using Turbo v8+, by default, the link will be prefetched on hover.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/link
 * @see https://turbo.hotwired.dev/handbook/drive#prefetching-links-on-hover
 */
final class LinkColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $href = $options['href'];

        if ($href instanceof \Closure) {
            $href = $href($view->vars['data'], $view->parent->data, $column);
        }

        $target = $options['target'];

        if ($target instanceof \Closure) {
            $target = $target($view->vars['data'], $view->parent->data, $column);
        }

        $view->vars = array_replace($view->vars, [
            'href' => $href,
            'target' => $target,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('href')
            ->default('#')
            ->allowedTypes('string', \Closure::class)
            ->info('Defines the URL to link to.')
        ;

        $resolver->define('target')
            ->default(null)
            ->allowedTypes('null', 'string', \Closure::class)
            ->info('Sets the value that will be used as a "target" HTML attribute.')
        ;
    }
}

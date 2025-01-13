<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HtmlColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'raw' => $options['raw'],
            'strip_tags' => $options['strip_tags'],
            'allowed_tags' => $options['allowed_tags'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('raw')
            ->default(true)
            ->allowedTypes('bool')
            ->info('Defines whether the value should be rendered as raw HTML.')
        ;

        /* @see https://www.php.net/strip_tags */
        $resolver->define('strip_tags')
            ->default(false)
            ->allowedTypes('bool')
            ->info('Defines whether the tags should be stripped. Internally uses the "strip_tags" function.')
        ;

        /* @see https://www.php.net/strip_tags */
        $resolver->define('allowed_tags')
            ->default(null)
            ->allowedTypes('null', 'string', 'string[]')
            ->info('Defines tags which should not be stripped if "strip_tags" is set to true, e.g. "<br><p>"')
        ;
    }
}

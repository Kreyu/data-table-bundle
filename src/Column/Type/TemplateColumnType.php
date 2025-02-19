<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TemplateColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        if (is_callable($templatePath = $options['template_path'])) {
            $templatePath = $templatePath($view->data, $column);
        }

        if (is_callable($templateVars = $options['template_vars'])) {
            $templateVars = $templateVars($view->data, $column);
        }

        $view->vars = array_merge($view->vars, [
            'template_path' => $templatePath,
            'template_vars' => $templateVars,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/template#template_path */
        $resolver->define('template_path')
            ->required()
            ->allowedTypes('string', 'callable')
            ->info('A path to the template that should be rendered.')
        ;

        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/template#template_vars */
        $resolver->define('template_vars')
            ->default([])
            ->allowedTypes('array', 'callable')
            ->info('An array of variables passed to the template.')
        ;
    }
}

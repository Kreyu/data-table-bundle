<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a column with value displayed as date range from date period object.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/date-period
 */
final class DatePeriodColumnType extends AbstractDateTimeColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        parent::buildValueView($view, $column, $options);

        $view->vars = array_replace($view->vars, [
            'separator' => $options['separator'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefault('format', 'd.m.Y');

        $resolver->define('separator')
            ->default(' - ')
            ->allowedTypes('null', 'string')
            ->info('A string used to visually separate start and end dates.')
        ;
    }
}

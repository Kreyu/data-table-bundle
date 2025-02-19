<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateTimeColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'format' => $options['format'],
            'timezone' => $options['timezone'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/date-time#format */
        $resolver->define('format')
            ->default('d.m.Y H:i:s')
            ->allowedTypes('string')
            ->info('A date time string format, supported by the PHP date() function.')
        ;

        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/date-time#format */
        $resolver->define('timezone')
            ->default(null)
            ->allowedTypes('null', 'bool', 'string', \DateTimeZone::class)
            ->info('Target timezone - null to use the default, false to leave unchanged.')
        ;
    }
}

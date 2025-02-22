<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Formatter\IntlFormatter;

/**
 * Represents a column with value displayed as a number.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/number
 */
final class NumberColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_merge($view->vars, [
            'use_intl_formatter' => $options['use_intl_formatter'],
            'intl_formatter_options' => $options['intl_formatter_options'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('use_intl_formatter')
            ->default(class_exists(IntlFormatter::class))
            ->allowedTypes('bool')
        ;

        $resolver->define('intl_formatter_options')
            ->default(function (OptionsResolver $resolver) {
                $resolver
                    ->setDefaults([
                        'attrs' => [],
                        'style' => 'decimal',
                    ])
                    ->setAllowedTypes('attrs', 'array')
                    ->setAllowedTypes('style', 'string')
                ;
            })
        ;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a column with monetary value, appropriately formatted and rendered with currency.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/money
 */
final class MoneyColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        if (is_callable($currency = $options['currency'])) {
            $currency = $currency($view->parent->data);
        }

        if (null !== ($divisor = $options['divisor'])) {
            $view->vars['value'] /= $divisor;
        }

        $view->vars = array_merge($view->vars, [
            'currency' => $currency,
            'divisor' => $divisor,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('currency')
            ->required()
            ->allowedTypes('string', 'callable')
        ;

        $resolver
            ->define('divisor')
            ->default(null)
            ->allowedTypes('null', 'int')
            ->allowedValues(static fn (?int $value) => 0 !== $value)
            ->info('A divisor used to divide the value before rendering.')
        ;
    }

    public function getParent(): string
    {
        return NumberColumnType::class;
    }
}

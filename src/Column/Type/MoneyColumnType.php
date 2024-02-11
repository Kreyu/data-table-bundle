<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class MoneyColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        if (is_callable($currency = $options['currency'])) {
            $currency = $currency($view->parent->data);
        }

        $view->vars = array_merge($view->vars, [
            'currency' => $currency,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('currency')
            ->setAllowedTypes('currency', ['string', 'callable'])
        ;
    }

    public function getParent(): ?string
    {
        return NumberColumnType::class;
    }
}

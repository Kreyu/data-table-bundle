<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PercentColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $callableOptions = [
            'symbol',
            'type',
        ];

        foreach ($callableOptions as $optionName) {
            if (is_callable($options[$optionName])) {
                $options[$optionName] = $options[$optionName]($view->parent->data);
            }
        }

        $view->vars = array_merge($view->vars, [
            'symbol' => $options['symbol'],
            'type' => $options['type'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'scale' => 0,
                'symbol' => '%',
                'type' => 'fractional',
            ])
            ->setAllowedTypes('symbol', ['bool', 'string'])
            ->setAllowedTypes('type', 'string')
            ->setAllowedValues('type', [
                'fractional',
                'integer',
            ])
        ;
    }

    public function getParent(): ?string
    {
        return NumberColumnType::class;
    }
}

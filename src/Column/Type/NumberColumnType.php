<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\Formatter\IntlFormatter;

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
        $resolver
            ->setDefaults([
                'use_intl_formatter' => class_exists(IntlFormatter::class),
                'intl_formatter_options' => function (OptionsResolver $resolver) {
                    $resolver
                        ->setDefaults([
                            'attrs' => [],
                            'style' => 'decimal',
                        ])
                        ->setAllowedTypes('attrs', 'array')
                        ->setAllowedTypes('style', 'string')
                    ;
                },
            ])
            ->setAllowedTypes('use_intl_formatter', 'bool')
        ;
    }

    public function getParent(): ?string
    {
        return TextColumnType::class;
    }
}

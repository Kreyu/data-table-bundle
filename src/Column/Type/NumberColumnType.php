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
            'badge' => $options['badge'],
        ]);

        if ($options['badge']) {
            $badgeClass = is_callable($options['badge']) ? $options['badge']($view->data) : $options['badge'];
            $view->vars['attr']['class'] = trim(($view->vars['attr']['class'] ?? '') . ' badge ' . $badgeClass);
        }
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
                'badge' => false,
            ])
            ->setAllowedTypes('use_intl_formatter', 'bool')
            ->setAllowedTypes('badge', ['bool', 'string', 'callable'])
            ->setInfo('badge', 'Defines whether the value should be rendered as a badge. Can be a boolean, string, or callable.')
        ;
    }

    public function getParent(): ?string
    {
        return TextColumnType::class;
    }
}

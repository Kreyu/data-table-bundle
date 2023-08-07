<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\I18n;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Column\Extension\AbstractColumnTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractIntlColumnTypeExtension extends AbstractColumnTypeExtension
{
    public function __construct(
        private readonly bool $intlFormatterEnabled = true,
        private readonly array $intlFormatterOptions = [],
    ) {
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_merge($view->vars, [
            'intl_formatter_enabled' => $options['intl_formatter_enabled'],
            'intl_formatter_options' => $options['intl_formatter_options'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'intl_formatter_enabled' => $this->intlFormatterEnabled,
                'intl_formatter_options' => function (OptionsResolver $resolver) {
                    $resolver
                        ->setDefaults($this->intlFormatterOptions + [
                            'attrs' => [],
                            'style' => 'decimal',
                        ])
                        ->setAllowedTypes('attrs', 'array')
                        ->setAllowedTypes('style', 'string')
                    ;
                },
            ])
            ->setAllowedTypes('intl_formatter_enabled', 'bool')
        ;
    }
}

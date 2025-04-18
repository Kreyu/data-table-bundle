<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;

/**
 * Represents a column with value displayed as "yes" or "no".
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/boolean
 */
final class BooleanColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'label_true' => $options['label_true'],
            'label_false' => $options['label_false'],
            'translation_key' => $view->value ? $options['label_true'] : $options['label_false'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('label_true')
            ->default('Yes')
            ->allowedTypes('string', TranslatableInterface::class)
            ->info('Label displayed when the value is truthy.')
        ;

        $resolver->define('label_false')
            ->default('No')
            ->allowedTypes('string', TranslatableInterface::class)
            ->info('Label displayed when the value is falsy.')
        ;

        $resolver->setDefault('value_translation_domain', 'KreyuDataTable');
    }
}

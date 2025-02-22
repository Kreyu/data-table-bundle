<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a column with checkboxes, one in its header, and one as its value.
 *
 * In most cases, it is not necessary to use this column type directly.
 * Instead, use the {@see DataTableBuilderInterface::addBatchAction()} method.
 * If at least one batch action is defined and visible, column of this type is added.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/checkbox
 */
final class CheckboxColumnType extends AbstractColumnType
{
    public function buildHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        $view->vars['identifier_name'] = $options['identifier_name'];
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars['identifier_name'] = $options['identifier_name'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('identifier_name')
            ->default('id')
            ->allowedTypes('string')
            ->info('The name of the identifier property.')
        ;

        $resolver->setDefaults([
            'label' => '□',
            'property_path' => 'id',
        ]);
    }
}

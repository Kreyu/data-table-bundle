<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/checkbox#identifier_name */
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

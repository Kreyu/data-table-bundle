<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CollectionColumnType extends AbstractColumnType
{
    public function buildColumn(ColumnBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('prototype_factory', $builder->getColumnFactory());
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'separator' => $options['separator'],
            'children' => $this->createChildrenColumnValueViews($view, $column, $options),
        ]);
    }

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        if (!is_array($options['export'])) {
            $options['export'] = [];
        }

        $options['export'] += [
            'entry_type' => $options['entry_type'],
            'entry_options' => $options['entry_options'],
            'separator' => $options['separator'],
        ];

        $view->value = $view->vars['value'] = implode($options['export']['separator'], array_map(
            static fn (ColumnValueView $view) => $view->vars['value'],
            $this->createChildrenColumnValueViews($view, $column, $options['export']),
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/collection#entry_type */
        $resolver->define('entry_type')
            ->default(TextColumnType::class)
            ->info('Column type to render for each item in the collection.')
            ->allowedTypes('string')
        ;

        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/collection#entry_options */
        $resolver->define('entry_options')
            ->default([])
            ->info('Options to pass to the column type for each item in the collection.')
            ->allowedTypes('array')
        ;

        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/collection#separator */
        $resolver->define('separator')
            ->default(', ')
            ->info('Separator to render between each item in the collection.')
            ->allowedTypes('null', 'string')
        ;
    }

    private function createChildrenColumnValueViews(ColumnValueView $view, ColumnInterface $column, array $options): array
    {
        /** @var ColumnFactoryInterface $prototypeFactory */
        $prototypeFactory = $column->getConfig()->getAttribute('prototype_factory');

        $children = [];

        foreach ($view->vars['value'] ?? [] as $index => $data) {
            // Create a virtual row view for the child column.
            $valueRowView = clone $view->parent;
            $valueRowView->origin = $view->parent;
            $valueRowView->index = $index;
            $valueRowView->data = $data;

            $prototype = $prototypeFactory->createNamed((string) $index, $options['entry_type'], $options['entry_options']);
            $prototype->setDataTable($column->getDataTable());

            $children[] = $prototype->createValueView($valueRowView);
        }

        return $children;
    }
}

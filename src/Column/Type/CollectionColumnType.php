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
        $resolver
            ->setDefaults([
                'entry_type' => TextColumnType::class,
                'entry_options' => [],
                'separator' => ', ',
            ])
            ->setAllowedTypes('entry_type', 'string')
            ->setAllowedTypes('entry_options', 'array')
            ->setAllowedTypes('separator', ['null', 'string'])
        ;
    }

    private function createChildrenColumnValueViews(ColumnValueView $view, ColumnInterface $column, array $options): array
    {
        /** @var ColumnFactoryInterface $prototypeFactory */
        $prototypeFactory = $column->getConfig()->getAttribute('prototype_factory');

        $prototype = $prototypeFactory->createNamed('__name__', $options['entry_type'], $options['entry_options']);

        $children = [];

        foreach ($view->vars['value'] ?? [] as $index => $data) {
            // Create a virtual row view for the child column.
            $valueRowView = clone $view->parent;
            $valueRowView->origin = $view->parent;
            $valueRowView->index = $index;
            $valueRowView->data = $data;

            $children[] = $prototype->createValueView($valueRowView);
        }

        return $children;
    }
}

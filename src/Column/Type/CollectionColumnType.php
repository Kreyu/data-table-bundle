<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryAwareInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionColumnType extends AbstractColumnType implements ColumnFactoryAwareInterface
{
    use ColumnFactoryAwareTrait;

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars['value'] = implode($options['separator'] ?? '', [...$view->vars['value']]);
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $children = [];

        foreach ($view->value ?? [] as $index => $data) {
            $child = $this->columnFactory->createNamed(
                name: $column->getName().'__'.($index + 1),
                type: $options['entry_type'],
                options: $options['entry_options'] + [
                    'property_path' => false,
                ],
            );

            // Create a virtual row view for the child column.
            $valueRowView = clone $view->parent;
            $valueRowView->origin = $view->parent;
            $valueRowView->index = $index;
            $valueRowView->data = $data;

            $children[] = $child->createValueView($valueRowView);
        }

        $view->vars = array_replace($view->vars, [
            'separator' => $options['separator'],
            'children' => $children,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'entry_type' => TextColumnType::class,
                'entry_options' => [],
                'separator' => ', ',
            ])
            ->setAllowedTypes('entry_type', ['string'])
            ->setAllowedTypes('entry_options', ['array'])
            ->setAllowedTypes('separator', ['null', 'string'])
            ->setInfo('entry_type', 'A fully-qualified class name of the column type to render each entry.')
            ->setInfo('entry_options', 'An array of options passed to the column type.')
            ->setInfo('separator', 'A string used to visually separate each entry.')
        ;
    }
}

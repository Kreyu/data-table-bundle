<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryAwareInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryAwareTrait;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CollectionColumnType extends AbstractColumnType implements ColumnFactoryAwareInterface
{
    use ColumnFactoryAwareTrait;

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->value = implode($options['export']['separator'], array_map(
            static fn (ColumnValueView $view) => $view->value,
            $this->createChildrenColumnValueViews($view, $column, $options['export']),
        ));
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'separator' => $options['separator'],
            'children' => $this->createChildrenColumnValueViews($view, $column, $options),
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
            ->addNormalizer('export', function (Options $options, bool|array $value): bool|array {
                if (true === $value) {
                    $value = [];
                }

                if (is_array($value)) {
                    $value += [
                        'separator' => $options['separator'],
                        'entry_type' => $options['entry_type'],
                        'entry_options' => $options['entry_options'],
                    ];
                }

                return $value;
            })
        ;
    }

    private function createChildrenColumnValueViews(ColumnValueView $view, ColumnInterface $column, array $options): array
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

        return $children;
    }
}

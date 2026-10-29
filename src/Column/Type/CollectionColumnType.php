<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Represents a column with value displayed as a list.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/collection
 */
final class CollectionColumnType extends AbstractColumnType
{
    public function __construct(
        private ?TranslatorInterface $translator = null,
    ) {}

    public function buildColumn(ColumnBuilderInterface $builder, array $options): void
    {
        $builder->setAttribute('prototype_factory', $builder->getColumnFactory());
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'separator' => $options['separator'],
            'separator_html' => $options['separator_html'],
            'separator_translatable' => $options['separator'] instanceof TranslatableInterface,
            'children' => $this->createChildrenColumnValueViews($view, $column, $options),
        ]);
    }

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        /** @var bool|array<array-key, mixed> $exportOptions */
        $exportOptions = $options['export'];

        if (!is_array($exportOptions)) {
            $exportOptions = [];
        }

        $exportOptions += [
            'entry_type' => $options['entry_type'],
            'entry_options' => $options['entry_options'],
            'separator' => $options['separator'],
            'separator_html' => $options['separator_html'],
        ];

        /** @var string|TranslatableInterface $separator */
        $separator = $exportOptions['separator'] ?? '';

        /** @var list<scalar|\Stringable|null> $childrenValues */
        $childrenValues = array_map(
            static fn (ColumnValueView $childView): mixed => $childView->vars['value'],
            $this->createChildrenColumnValueViews($view, $column, $exportOptions),
        );

        $value = implode($this->translateSeparator($separator), $childrenValues);

        $view->value = $value;
        $view->vars['value'] = $value;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/collection#entry-type */
        $resolver
            ->define('entry_type')
            ->default(TextColumnType::class)
            ->info('Column type to render for each item in the collection.')
            ->allowedTypes('string')
        ;

        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/collection#entry-options */
        $resolver
            ->define('entry_options')
            ->default([])
            ->info('Options to pass to the column type for each item in the collection.')
            ->allowedTypes('array')
        ;

        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/collection#separator */
        $resolver
            ->define('separator')
            ->default(', ')
            ->info('Separator to render between each item in the collection.')
            ->allowedTypes('null', 'string', TranslatableInterface::class)
        ;

        /* @see https://data-table-bundle.swroblewski.pl/reference/types/column/collection#separator-html */
        $resolver
            ->define('separator_html')
            ->default(false)
            ->info('Determines whether the separator should be rendered as HTML instead of text.')
            ->allowedTypes('bool')
        ;
    }

    private function translateSeparator(string|TranslatableInterface $separator): string
    {
        if (is_string($separator)) {
            return $separator;
        }

        $translator = $this->translator;

        if ($translator === null) {
            return $separator instanceof \Stringable ? (string) $separator : '';
        }

        return $separator->trans($translator);
    }

    /**
     * @param array<array-key, mixed> $options
     *
     * @return list<ColumnValueView>
     */
    private function createChildrenColumnValueViews(
        ColumnValueView $view,
        ColumnInterface $column,
        array $options,
    ): array {
        /** @var ColumnFactoryInterface $prototypeFactory */
        $prototypeFactory = $column->getConfig()->getAttribute('prototype_factory');

        /** @var class-string<ColumnTypeInterface> $entryType */
        $entryType = $options['entry_type'];

        /** @var array<string, mixed> $entryOptions */
        $entryOptions = $options['entry_options'];

        $hasEntryPropertyPath = ($entryOptions['property_path'] ?? false) !== false;

        if (!$hasEntryPropertyPath) {
            $entryOptions += ['getter' => static fn (mixed $value): mixed => $value];
        }

        /** @var iterable<array-key, mixed> $collection */
        $collection = $view->vars['value'] ?? [];

        $children = [];

        /** @var mixed $data */
        foreach ($collection as $index => $data) {
            if (!is_int($index)) {
                throw new UnexpectedTypeException($index, 'int');
            }

            $valueRowView = clone $view->parent;
            $valueRowView->origin = $view->parent;
            $valueRowView->index = $index;
            $valueRowView->data = $data;

            $prototype = $prototypeFactory->createNamed((string) $index, $entryType, $entryOptions);
            $prototype->setDataTable($column->getDataTable());

            $children[] = $prototype->createValueView($valueRowView);
        }

        return $children;
    }
}

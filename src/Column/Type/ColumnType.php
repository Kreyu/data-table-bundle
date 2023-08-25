<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Kreyu\Bundle\DataTableBundle\Util\StringUtil;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPathInterface;
use Symfony\Component\Translation\TranslatableMessage;

final class ColumnType implements ColumnTypeInterface
{
    public function buildColumn(ColumnBuilderInterface $builder, array $options): void
    {
        $builder
            ->setPropertyPath($options['property_path'] ?: null)
            ->setSortPropertyPath(is_string($options['sort']) ? $options['sort'] : null)
            ->setSortable(false !== $options['sort'])
            ->setExportable(false !== $options['export'])
        ;
    }

    public function buildHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        $dataTable = $column->getDataTable();
        $sortColumnData = $dataTable->getSortingData()?->getColumn($column);

        $headerRowView = $view->parent;
        $dataTableView = $headerRowView->parent;

        $view->vars = array_replace($view->vars, [
            'name' => $column->getName(),
            'column' => $view,
            'row' => $headerRowView,
            'data_table' => $dataTableView,
            'block_prefixes' => $this->getColumnBlockPrefixes($column, $options),
            'label' => $options['label'] ?? StringUtil::camelToSentence($column->getName()),
            'translation_domain' => $options['header_translation_domain'] ?? $dataTableView->vars['translation_domain'] ?? null,
            'translation_parameters' => $options['header_translation_parameters'],
            'sort_parameter_name' => $dataTable->getConfig()->getSortParameterName(),
            'attr' => $options['header_attr'],
            'sorted' => null !== $sortColumnData,
            'sort_field' => $column->getSortPropertyPath(),
            'sort_direction' => $sortColumnData?->getDirection(),
            'sortable' => $column->getConfig()->isSortable(),
            'export' => $column->getConfig()->isExportable(),
        ]);
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $rowData = $view->parent->data;

        $normData = $this->getNormDataFromRowData($rowData, $column, $options);
        $viewData = $this->getViewDataFromNormData($normData, $column, $options);

        $view->data = $normData;
        $view->value = $viewData;

        if (is_callable($attr = $options['value_attr'])) {
            $attr = $attr($normData, $rowData);
        }

        $view->vars = array_replace($view->vars, [
            'row' => $view->parent,
            'data_table' => $view->parent->parent,
            'block_prefixes' => $this->getColumnBlockPrefixes($column, $options),
            'data' => $view->data,
            'value' => $view->value,
            'translation_domain' => $options['value_translation_domain'] ?? $view->parent->vars['translation_domain'] ?? null,
            'translation_parameters' => $options['value_translation_parameters'] ?? [],
            'attr' => $attr,
        ]);
    }

    public function buildExportHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        if (true === $options['export']) {
            $options['export'] = [];
        }

        $options['export'] += [
            'getter' => $options['getter'],
            'property_path' => $options['property_path'],
            'formatter' => $options['formatter'],
        ];

        $translationDomain = $options['export']['value_translation_domain']
            ?? $options['value_translation_domain']
            ?? $view->parent->vars['translation_domain']
            ?? null;

        $view->vars = array_replace($view->vars, [
            'label' => $options['label'] ?? StringUtil::camelToSentence($column->getName()),
            'translation_domain' => $translationDomain,
            'translation_parameters' => $options['header_translation_parameters'] ?? [],
        ]);
    }

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $rowData = $view->parent->data;

        if (true === $options['export']) {
            $options['export'] = [];
        }

        $options['export'] += [
            'getter' => $options['getter'],
            'property_path' => $options['property_path'],
            'property_accessor' => $options['property_accessor'],
            'formatter' => $options['formatter'],
        ];

        $normData = $this->getNormDataFromRowData($rowData, $column, $options['export']);
        $viewData = $this->getViewDataFromNormData($normData, $column, $options['export']);

        $view->value = $viewData;

        $translationDomain = $options['export']['value_translation_domain']
            ?? $options['value_translation_domain']
            ?? $view->parent->vars['translation_domain']
            ?? null;

        $view->vars = array_replace($view->vars, [
            'value' => $viewData,
            'translation_domain' => $translationDomain,
            'translation_parameters' => $options['value_translation_parameters'] ?? [],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label' => null,
                'header_translation_domain' => null,
                'header_translation_parameters' => [],
                'value_translation_domain' => false,
                'value_translation_parameters' => [],
                'block_name' => null,
                'block_prefix' => null,
                'sort' => false,
                'export' => false,
                'formatter' => null,
                'property_path' => null,
                'property_accessor' => PropertyAccess::createPropertyAccessor(),
                'getter' => null,
                'header_attr' => [],
                'value_attr' => [],
            ])
            ->setAllowedTypes('label', ['null', 'string', TranslatableMessage::class])
            ->setAllowedTypes('header_translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('header_translation_parameters', ['null', 'array'])
            ->setAllowedTypes('value_translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('value_translation_parameters', ['array'])
            ->setAllowedTypes('block_name', ['null', 'string'])
            ->setAllowedTypes('block_prefix', ['null', 'string'])
            ->setAllowedTypes('sort', ['bool', 'string'])
            ->setAllowedTypes('export', ['bool', 'array'])
            ->setAllowedTypes('formatter', ['null', 'callable'])
            ->setAllowedTypes('property_path', ['null', 'bool', 'string', PropertyPathInterface::class])
            ->setAllowedTypes('property_accessor', [PropertyAccessorInterface::class])
            ->setAllowedTypes('getter', ['null', 'callable'])
            ->setAllowedTypes('header_attr', ['array'])
            ->setAllowedTypes('value_attr', ['array', 'callable'])
            ->setInfo('label', 'A user-friendly label that describes a column.')
            ->setInfo('header_translation_domain', 'Translation domain used to translate the column header.')
            ->setInfo('header_translation_parameters', 'Parameters used within the column header translation.')
            ->setInfo('value_translation_domain', 'Translation domain used to translate the column value.')
            ->setInfo('value_translation_parameters', 'Parameters used within the column value translation.')
            ->setInfo('block_name', 'Name of the block that renders the column.')
            ->setInfo('block_prefix', 'A custom prefix of the block name that renders the column.')
            ->setInfo('sort', 'Determines whether the column can be sorted (and optionally on what path).')
            ->setInfo('export', 'Determines whether the column can be exported (and optionally with custom options).')
            ->setInfo('formatter', 'A formatter used to format the column norm data to the view data.')
            ->setInfo('property_path', 'Property path used to retrieve the column norm data from the row data.')
            ->setInfo('property_accessor', 'An instance of property accessor used to retrieve column norm data from the row data.')
            ->setInfo('getter', 'A callable data accessor used to retrieve column norm data from the row data manually, instead of property accessor.')
            ->setInfo('header_attr', 'An array of attributes (e.g. HTML attributes) passed to the header view.')
            ->setInfo('value_attr', 'An array of attributes (e.g. HTML attributes) passed to the column value view.')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'column';
    }

    public function getParent(): ?string
    {
        return null;
    }

    /**
     * Retrieves the column norm data from the row data by either:.
     *
     * - using the "getter" option;
     * - using the property accessor with the "property_path" option;
     * - falling back to the unmodified column data;
     */
    private function getNormDataFromRowData(mixed $data, ColumnInterface $column, array $options): mixed
    {
        if (null === $data) {
            return null;
        }

        if (is_callable($getter = $options['getter'])) {
            return $getter($data, $column, $options);
        }

        $propertyPath = $options['property_path'] ?? $column->getName();

        if (is_string($propertyPath) && (is_array($data) || is_object($data))) {
            return $options['property_accessor']->getValue($data, $propertyPath);
        }

        return $data;
    }

    /**
     * Retrieves the column view data from the norm data by applying the formatter if given.
     */
    private function getViewDataFromNormData(mixed $data, ColumnInterface $column, array $options): mixed
    {
        if (null === $data) {
            return null;
        }

        if (is_callable($formatter = $options['formatter'])) {
            $data = $formatter($data, $column, $options);
        }

        return $data;
    }

    /**
     * Retrieves the column block prefixes, respecting the type hierarchy.
     *
     * For example, take a look at the NumberColumnType. It is based on the TextColumnType,
     * which is based on the ColumnType, therefore its block prefixes are: ["number", "text", "column"].
     *
     * @return array<string>
     */
    private function getColumnBlockPrefixes(ColumnInterface $column, array $options): array
    {
        $type = $column->getConfig()->getType();

        $blockPrefixes = [
            $type->getBlockPrefix(),
        ];

        while (null !== $type->getParent()) {
            $blockPrefixes[] = ($type = $type->getParent())->getBlockPrefix();
        }

        if ($blockPrefix = $options['block_prefix']) {
            array_unshift($blockPrefixes, $blockPrefix);
        }

        return array_unique($blockPrefixes);
    }
}

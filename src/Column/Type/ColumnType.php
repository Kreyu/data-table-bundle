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
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ColumnType implements ColumnTypeInterface
{
    public function __construct(
        private ?TranslatorInterface $translator = null,
    ) {
    }

    public function buildColumn(ColumnBuilderInterface $builder, array $options): void
    {
        $builder
            ->setPropertyPath($options['property_path'] ?: null)
            ->setSortPropertyPath(is_string($options['sort']) ? $options['sort'] : null)
            ->setPriority($options['priority'])
            ->setVisible($options['visible'])
            ->setPersonalizable($options['personalizable'])
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
        $viewData = $this->getViewDataFromNormData($normData, $rowData, $column, $options);

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
            'translation_domain' => $options['value_translation_domain'] ?? $view->parent->parent->vars['translation_domain'] ?? null,
            'translation_parameters' => $options['value_translation_parameters'] ?? [],
            'attr' => $attr,
        ]);
    }

    public function buildExportHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        if (false === $options['export']) {
            return;
        }

        if (true === $options['export']) {
            $options['export'] = [];
        }

        $options['export'] += [
            'getter' => $options['getter'],
            'property_path' => $options['property_path'],
            'formatter' => $options['formatter'],
        ];

        $label = $options['label'] ?? StringUtil::camelToSentence($column->getName());

        if ($this->translator) {
            if ($label instanceof TranslatableInterface) {
                $label = $label->trans($this->translator, $this->translator->getLocale());
            } else {
                $translationDomain = $options['export']['header_translation_domain']
                    ?? $options['header_translation_domain']
                    ?? $view->parent->parent->vars['translation_domain']
                    ?? false;

                if ($translationDomain) {
                    $label = $this->translator->trans(
                        id: $label,
                        parameters: $options['header_translation_parameters'],
                        domain: $translationDomain,
                    );
                }
            }
        }

        $view->vars['label'] = $label;
    }

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        if (false === $options['export']) {
            return;
        }

        if (true === $options['export']) {
            $options['export'] = [];
        }

        $options['export'] += [
            'getter' => $options['getter'],
            'property_path' => $options['property_path'],
            'property_accessor' => $options['property_accessor'],
            'formatter' => $options['formatter'],
        ];

        $rowData = $view->parent->data;

        $normData = $this->getNormDataFromRowData($rowData, $column, $options['export']);
        $viewData = $this->getViewDataFromNormData($normData, $rowData, $column, $options['export']);

        if ($this->translator && is_string($viewData)) {
            $translationDomain = $options['export']['value_translation_domain']
                ?? $options['value_translation_domain']
                ?? $view->parent->parent->vars['translation_domain']
                ?? false;

            if ($translationDomain) {
                $viewData = $this->translator->trans(
                    id: $viewData,
                    parameters: $options['value_translation_parameters'],
                    domain: $translationDomain,
                );
            }
        }

        $view->value = $viewData;

        $view->vars['value'] = $viewData;
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
                'priority' => 0,
                'visible' => true,
                'personalizable' => true,
            ])
            ->setAllowedTypes('label', ['null', 'string', TranslatableInterface::class])
            ->setAllowedTypes('header_translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('header_translation_parameters', ['null', 'array'])
            ->setAllowedTypes('value_translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('value_translation_parameters', 'array')
            ->setAllowedTypes('block_name', ['null', 'string'])
            ->setAllowedTypes('block_prefix', ['null', 'string'])
            ->setAllowedTypes('sort', ['bool', 'string'])
            ->setAllowedTypes('export', ['bool', 'array'])
            ->setAllowedTypes('formatter', ['null', 'callable'])
            ->setAllowedTypes('property_path', ['null', 'bool', 'string', PropertyPathInterface::class])
            ->setAllowedTypes('property_accessor', PropertyAccessorInterface::class)
            ->setAllowedTypes('getter', ['null', 'callable'])
            ->setAllowedTypes('header_attr', 'array')
            ->setAllowedTypes('value_attr', ['array', 'callable'])
            ->setAllowedTypes('priority', 'int')
            ->setAllowedTypes('visible', 'bool')
            ->setAllowedTypes('personalizable', 'bool')
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
    private function getNormDataFromRowData(mixed $rowData, ColumnInterface $column, array $options): mixed
    {
        if (null === $rowData) {
            return null;
        }

        if (is_callable($getter = $options['getter'])) {
            return $getter($rowData, $column, $options);
        }

        $propertyPath = $options['property_path'] ?? $column->getName();

        if ((is_string($propertyPath) || $propertyPath instanceof PropertyPathInterface) && (is_array($rowData) || is_object($rowData))) {
            return $options['property_accessor']->getValue($rowData, $propertyPath);
        }

        return $rowData;
    }

    /**
     * Retrieves the column view data from the norm data by applying the formatter if given.
     */
    private function getViewDataFromNormData(mixed $normData, mixed $rowData, ColumnInterface $column, array $options): mixed
    {
        if (null === $normData) {
            return null;
        }

        $viewData = $normData;

        if (is_callable($formatter = $options['formatter'])) {
            $viewData = $formatter($normData, $rowData, $column, $options);
        }

        return $viewData;
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

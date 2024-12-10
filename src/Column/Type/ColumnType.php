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
        $sortPropertyPath = null;

        if (true === $options['sort']) {
            $sortPropertyPath = $builder->getName();
        } elseif (is_string($options['sort'])) {
            $sortPropertyPath = $options['sort'];
        }

        $builder
            ->setPropertyPath($options['property_path'] ?? $builder->getName() ?: null)
            ->setSortPropertyPath($sortPropertyPath)
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

        $translationParameters = $options['value_translation_parameters'];

        if (is_callable($translationParameters)) {
            $translationParameters = $translationParameters($normData, $rowData);
        }

        $view->vars = array_replace($view->vars, [
            'name' => $column->getName(),
            'column' => $view,
            'row' => $view->parent,
            'data_table' => $view->parent->parent,
            'block_prefixes' => $this->getColumnBlockPrefixes($column, $options),
            'data' => $view->data,
            'value' => $view->value,
            'translation_domain' => $options['value_translation_domain'] ?? $view->parent->parent->vars['translation_domain'] ?? null,
            'translation_parameters' => $translationParameters ?? [],
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

        $options['export']['label'] ??= $options['label'] ?? StringUtil::camelToSentence($column->getName());
        $options['export']['header_translation_domain'] ??= $options['header_translation_domain'] ?? $view->parent->parent->vars['translation_domain'] ?? false;
        $options['export']['header_translation_parameters'] ??= $options['header_translation_parameters'] ?? [];

        $label = $options['export']['label'];

        if ($this->translator) {
            if ($label instanceof TranslatableInterface) {
                $locale = null;

                if (method_exists(TranslatableInterface::class, 'getLocale')) {
                    $locale = $this->translator->getLocale();
                }

                $label = $label->trans($this->translator, $locale);
            } else {
                $translationDomain = $options['export']['header_translation_domain'];
                $translationParameters = $options['export']['header_translation_parameters'];

                if ($translationDomain) {
                    $label = $this->translator->trans($label, $translationParameters, $translationDomain);
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

        $options['export']['getter'] ??= $options['getter'];
        $options['export']['property_path'] ??= $options['property_path'];
        $options['export']['property_accessor'] ??= $options['property_accessor'];
        $options['export']['formatter'] ??= $options['formatter'];
        $options['export']['value_translation_domain'] ??= $options['value_translation_domain'] ?? $view->parent->parent->vars['translation_domain'] ?? false;
        $options['export']['value_translation_parameters'] ??= $options['value_translation_parameters'] ?? [];

        $rowData = $view->parent->data;

        $normData = $this->getNormDataFromRowData($rowData, $column, $options['export']);
        $viewData = $this->getViewDataFromNormData($normData, $rowData, $column, $options['export']);

        if ($this->translator && (is_string($viewData) || $viewData instanceof TranslatableInterface)) {
            if ($viewData instanceof TranslatableInterface) {
                $locale = null;

                if (method_exists(TranslatableInterface::class, 'getLocale')) {
                    $locale = $this->translator->getLocale();
                }

                $viewData = $viewData->trans($this->translator, $locale);
            } else {
                $translationDomain = $options['export']['value_translation_domain'];
                $translationParameters = $options['export']['value_translation_parameters'];

                if (is_callable($translationParameters)) {
                    $translationParameters = $translationParameters($normData, $rowData);
                }

                if ($translationDomain) {
                    $viewData = $this->translator->trans(
                        id: $viewData,
                        parameters: $translationParameters,
                        domain: $translationDomain,
                    );
                }
            }
        }

        $view->data = $normData;
        $view->value = $viewData;

        $view->vars['data'] = $normData;
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
            ->setAllowedTypes('value_translation_parameters', ['array', 'callable'])
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

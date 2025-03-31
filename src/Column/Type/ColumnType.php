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

/**
 * Represents a base column with basic functionality, used as a parent for other column types.
 *
 * @see https://data-table-bundle.swroblewski.pl/reference/types/column/collection
 */
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
        $valueRowView = $view->parent;
        $dataTableView = $valueRowView->parent;

        $rowData = $view->getRowData();

        $data = $this->getColumnDataFromRowData($rowData, $column, $options);
        $value = $this->getColumnValueFromColumnData($data, $rowData, $column, $options);

        $view->data = $data;
        $view->value = $value;

        if (is_callable($attr = $options['value_attr'])) {
            $attr = $attr($data, $rowData);
        }

        if (is_callable($translationParameters = $options['value_translation_parameters'])) {
            $translationParameters = $translationParameters($data, $rowData);
        }

        $view->vars = array_replace($view->vars, [
            'name' => $column->getName(),
            'column' => $view,
            'row' => $valueRowView,
            'data_table' => $dataTableView,
            'block_prefixes' => $this->getColumnBlockPrefixes($column, $options),
            'data' => $data,
            'value' => $value,
            'translation_domain' => $options['value_translation_domain'] ?? $dataTableView->vars['translation_domain'] ?? null,
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

        $data = $this->getColumnDataFromRowData($rowData, $column, $options['export']);
        $value = $this->getColumnValueFromColumnData($data, $rowData, $column, $options['export']);

        if ($this->translator && (is_string($value) || $value instanceof TranslatableInterface)) {
            if ($value instanceof TranslatableInterface) {
                $locale = null;

                if (method_exists(TranslatableInterface::class, 'getLocale')) {
                    $locale = $this->translator->getLocale();
                }

                $value = $value->trans($this->translator, $locale);
            } else {
                $translationDomain = $options['export']['value_translation_domain'];
                $translationParameters = $options['export']['value_translation_parameters'];

                if (is_callable($translationParameters)) {
                    $translationParameters = $translationParameters($data, $rowData);
                }

                if ($translationDomain) {
                    $value = $this->translator->trans(
                        id: $value,
                        parameters: $translationParameters,
                        domain: $translationDomain,
                    );
                }
            }
        }

        $view->vars['data'] = $view->data = $data;
        $view->vars['value'] = $view->value = $value;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->define('label')
            ->default(null)
            ->allowedTypes('null', 'string', TranslatableInterface::class)
            ->info('Label displayed in column header - null to default to sentence cased column name.')
        ;

        $resolver->define('header_translation_domain')
            ->default(null)
            ->allowedTypes('null', 'bool', 'string')
            ->info('Translation domain used to translate the column header - set to false to disable translation.')
        ;

        $resolver->define('header_translation_parameters')
            ->default([])
            ->allowedTypes('null', 'array')
            ->info('Translation parameters used to translate the column header.')
        ;

        $resolver->define('value_translation_domain')
            ->default(false)
            ->allowedTypes('null', 'bool', 'string')
            ->info('Translation parameters used to translate the column value - set to false to disable translation.')
        ;

        $resolver->define('value_translation_parameters')
            ->default([])
            ->allowedTypes('array', 'callable')
            ->info('Translation parameters used to translate the column value.')
        ;

        $resolver->define('block_prefix')
            ->default(null)
            ->allowedTypes('null', 'string')
            ->info('Defines custom block prefix to use when rendering the column.')
        ;

        $resolver->define('sort')
            ->default(false)
            ->allowedTypes('bool', 'string')
            ->info('Defines whether the column is sortable. Passing a string sets the sorting path.')
        ;

        $resolver->define('export')
            ->default(false)
            ->allowedTypes('bool', 'array')
            ->info('Defines whether the column is exportable. You can pass an array of options to differentiate them during an export.')
        ;

        $resolver->define('formatter')
            ->default(null)
            ->allowedTypes('null', 'callable')
            ->info('Formatter to use on non-empty value to customize it even further before rendering. Column value and row data are passed as arguments.')
        ;

        $resolver->define('property_path')
            ->default(null)
            ->allowedTypes('null', 'bool', 'string', PropertyPathInterface::class)
            ->info('Path to use by property accessor component to retrieve the column value from row data. Defaults to column name.')
        ;

        $resolver->define('property_accessor')
            ->default(PropertyAccess::createPropertyAccessor())
            ->allowedTypes(PropertyAccessorInterface::class)
            ->info('An instance of property accessor to use to retrieve the value.')
        ;

        $resolver->define('getter')
            ->default(null)
            ->allowedTypes('null', 'callable')
            ->info('Callable used to retrieve column value from row data. If set, it is used instead of property accessor.')
        ;

        $resolver->define('header_attr')
            ->default([])
            ->allowedTypes('array')
            ->info('Extra HTML attributes to render on the column header.')
        ;

        $resolver->define('value_attr')
            ->default([])
            ->allowedTypes('array', 'callable')
            ->info('Extra HTML attributes to render on the column value.')
        ;

        $resolver->define('priority')
            ->default(0)
            ->allowedTypes('int')
            ->info('Defines the priority of the column - the higher the priority, the earlier the column will be rendered.')
        ;

        $resolver->define('visible')
            ->default(true)
            ->allowedTypes('bool')
            ->info('Defines the visibility of the column.')
        ;

        $resolver->define('personalizable')
            ->default(true)
            ->allowedTypes('bool')
            ->info('Defines whether the column can be personalized by the user in personalization feature.')
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

    private function getColumnDataFromRowData(mixed $rowData, ColumnInterface $column, array $options): mixed
    {
        if (null === $rowData) {
            return null;
        }

        if (is_callable($getter = $options['getter'])) {
            return $getter($rowData, $column, $options);
        }

        if ($rowData instanceof \UnitEnum) {
            return $rowData;
        }

        $propertyPath = $options['property_path'] ?? $column->getName();

        if ((is_string($propertyPath) || $propertyPath instanceof PropertyPathInterface) && (is_array($rowData) || is_object($rowData))) {
            return $options['property_accessor']->getValue($rowData, $propertyPath);
        }

        return $rowData;
    }

    private function getColumnValueFromColumnData(mixed $data, mixed $rowData, ColumnInterface $column, array $options): mixed
    {
        if (null === $data) {
            return null;
        }

        $value = $data;

        if (is_callable($formatter = $options['formatter'])) {
            $value = $formatter($data, $rowData, $column, $options);
        }

        return $value;
    }

    /**
     * Retrieves the column block prefixes, respecting the type hierarchy.
     *
     * For example, take a look at the NumberColumnType. It is based on the ColumnType,
     * therefore its block prefixes are: ["number", "column"].
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

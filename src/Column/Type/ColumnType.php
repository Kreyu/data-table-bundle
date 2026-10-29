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
    ) {}

    public function buildColumn(ColumnBuilderInterface $builder, array $options): void
    {
        /** @var bool|string $sort */
        $sort = $options['sort'];

        /** @var int $priority */
        $priority = $options['priority'];

        /** @var bool $visible */
        $visible = $options['visible'];

        /** @var bool $personalizable */
        $personalizable = $options['personalizable'];

        $builder
            ->setPropertyPath($this->getPropertyPath($builder, $options))
            ->setSortPropertyPath($this->getSortPropertyPath($builder, $sort))
            ->setPriority($priority)
            ->setVisible($visible)
            ->setPersonalizable($personalizable)
            ->setSortable($sort !== false)
            ->setExportable($options['export'] !== false)
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
            'translation_domain' =>
                $options['header_translation_domain'] ?? $dataTableView->vars['translation_domain'] ?? null,
            'translation_parameters' => $options['header_translation_parameters'],
            'sort_parameter_name' => $dataTable->getConfig()->getSortParameterName(),
            'attr' => $options['header_attr'],
            'sorted' => null !== $sortColumnData && 'none' !== $sortColumnData->getDirection(),
            'sort_field' => $column->getSortPropertyPath(),
            'sort_direction' => $sortColumnData?->getDirection(),
            'sortable' => $column->getConfig()->isSortable(),
            'export' => $column->getConfig()->isExportable(),
            'filter_form' => null,
        ]);
    }

    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $valueRowView = $view->parent;
        $dataTableView = $valueRowView->parent;

        /** @var mixed $rowData */
        $rowData = $view->getRowData();

        /** @var mixed $data */
        $data = $this->getColumnDataFromRowData($rowData, $column, $options);

        /** @var mixed $value */
        $value = $this->getColumnValueFromColumnData($data, $rowData, $column, $options);

        $view->data = $data;
        $view->value = $value;

        /** @var array<array-key, mixed>|callable $attr */
        $attr = $options['value_attr'];

        /** @var array<array-key, mixed>|callable $cellAttr */
        $cellAttr = $options['value_cell_attr'];

        /** @var array<array-key, mixed>|callable $translationParameters */
        $translationParameters = $options['value_translation_parameters'];

        /** @var string|TranslatableInterface|null $translationKey */
        $translationKey = $options['value_translation_key'];

        /** @var bool|string|null $translationDomain */
        $translationDomain = $options['value_translation_domain'] ?? $dataTableView->vars['translation_domain'] ?? null;

        $translatable = $value instanceof TranslatableInterface || $translationDomain !== false;

        if ($translatable && (is_string($value) || $value instanceof TranslatableInterface)) {
            $translationKey ??= $value;
        }

        $view->vars = array_replace($view->vars, [
            'name' => $column->getName(),
            'column' => $view,
            'row' => $valueRowView,
            'data_table' => $dataTableView,
            'block_prefixes' => $this->getColumnBlockPrefixes($column, $options),
            'data' => $data,
            'value' => $value,
            'translatable' => $translatable,
            'is_instance_of_translatable' => $value instanceof TranslatableInterface,
            'translation_key' => $translationKey,
            'translation_domain' => $translationDomain,
            'translation_parameters' => $this->resolveRowDataOption($translationParameters, $data, $rowData),
            'attr' => $this->resolveRowDataOption($attr, $data, $rowData),
            'cell_attr' => $this->resolveRowDataOption($cellAttr, $data, $rowData),
        ]);
    }

    public function buildExportHeaderView(ColumnHeaderView $view, ColumnInterface $column, array $options): void
    {
        $exportOptions = $this->getExportOptions($options);

        if ($exportOptions === null) {
            return;
        }

        /** @var string|TranslatableInterface $label */
        $label = $exportOptions['label'] ?? $options['label'] ?? StringUtil::camelToSentence($column->getName());

        /** @var bool|string|null $translationDomain */
        $translationDomain =
            $exportOptions['header_translation_domain']
            ?? $options['header_translation_domain']
            ?? $view->getDataTable()->vars['translation_domain']
            ?? false;

        /** @var array<array-key, mixed> $translationParameters */
        $translationParameters =
            $exportOptions['header_translation_parameters'] ?? $options['header_translation_parameters'] ?? [];

        $view->vars['label'] = $this->translateExportLabel($label, $translationDomain, $translationParameters);
    }

    public function buildExportValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $exportOptions = $this->getExportOptions($options);

        if ($exportOptions === null) {
            return;
        }

        $exportOptions['getter'] ??= $options['getter'];
        $exportOptions['property_path'] ??= $options['property_path'];
        $exportOptions['property_accessor'] ??= $options['property_accessor'];
        $exportOptions['formatter'] ??= $options['formatter'];
        $exportOptions['value_translation_key'] ??= $options['value_translation_key'];
        $exportOptions['value_translation_domain'] ??=
            $options['value_translation_domain'] ?? $view->getDataTable()->vars['translation_domain'] ?? false;
        $exportOptions['value_translation_parameters'] ??= $options['value_translation_parameters'] ?? [];

        /** @var mixed $rowData */
        $rowData = $view->getRowData();

        /** @var mixed $data */
        $data = $this->getColumnDataFromRowData($rowData, $column, $exportOptions);

        /** @var mixed $value */
        $value = $this->getColumnValueFromColumnData($data, $rowData, $column, $exportOptions);

        /** @var mixed $translatedValue */
        $translatedValue = $this->translateExportValue($value, $data, $rowData, $exportOptions);

        $view->data = $data;
        $view->value = $translatedValue;
        $view->vars['data'] = $data;
        $view->vars['value'] = $translatedValue;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->define('label')
            ->default(null)
            ->allowedTypes('null', 'string', TranslatableInterface::class)
            ->info('Label displayed in column header - null to default to sentence cased column name.')
        ;

        $resolver
            ->define('header_translation_domain')
            ->default(null)
            ->allowedTypes('null', 'bool', 'string')
            ->info('Translation domain used to translate the column header - set to false to disable translation.')
        ;

        $resolver
            ->define('header_translation_parameters')
            ->default([])
            ->allowedTypes('null', 'array')
            ->info('Translation parameters used to translate the column header.')
        ;

        $resolver
            ->define('value_translation_key')
            ->default(null)
            ->allowedTypes('null', 'string')
            ->info('Translation key used to translate the column value - used instead of column value if given.')
        ;

        $resolver
            ->define('value_translation_domain')
            ->default(false)
            ->allowedTypes('null', 'bool', 'string')
            ->info('Translation parameters used to translate the column value - set to false to disable translation.')
        ;

        $resolver
            ->define('value_translation_parameters')
            ->default([])
            ->allowedTypes('array', 'callable')
            ->info('Translation parameters used to translate the column value.')
        ;

        $resolver
            ->define('block_prefix')
            ->default(null)
            ->allowedTypes('null', 'string')
            ->info('Defines custom block prefix to use when rendering the column.')
        ;

        $resolver
            ->define('sort')
            ->default(false)
            ->allowedTypes('bool', 'string')
            ->info('Defines whether the column is sortable. Passing a string sets the sorting path.')
        ;

        $resolver
            ->define('filter')
            ->default(null)
            ->allowedTypes('null', 'string')
            ->info('Filter type rendered in the column header. Adds a filter named after the column.')
        ;

        $resolver
            ->define('filter_options')
            ->default([])
            ->allowedTypes('array')
            ->info('Options passed to the column header filter.')
        ;

        $resolver
            ->define('export')
            ->default(false)
            ->allowedTypes('bool', 'array')
            ->info(
                'Defines whether the column is exportable. You can pass an array of options to differentiate them during an export.',
            )
        ;

        $resolver
            ->define('formatter')
            ->default(null)
            ->allowedTypes('null', 'callable')
            ->info(
                'Formatter to use on non-empty value to customize it even further before rendering. Column value and row data are passed as arguments.',
            )
        ;

        $resolver
            ->define('property_path')
            ->default(null)
            ->allowedTypes('null', 'bool', 'string', PropertyPathInterface::class)
            ->info(
                'Path to use by property accessor component to retrieve the column value from row data. Defaults to column name.',
            )
        ;

        $resolver
            ->define('property_accessor')
            ->default(PropertyAccess::createPropertyAccessor())
            ->allowedTypes(PropertyAccessorInterface::class)
            ->info('An instance of property accessor to use to retrieve the value.')
        ;

        $resolver
            ->define('getter')
            ->default(null)
            ->allowedTypes('null', 'callable')
            ->info(
                'Callable used to retrieve column value from row data. If set, it is used instead of property accessor.',
            )
        ;

        $resolver
            ->define('header_attr')
            ->default([])
            ->allowedTypes('array')
            ->info('Extra HTML attributes to render on the column header.')
        ;

        $resolver
            ->define('value_attr')
            ->default([])
            ->allowedTypes('array', 'callable')
            ->info('Extra HTML attributes to render on the column value.')
        ;

        $resolver
            ->define('value_cell_attr')
            ->default([])
            ->allowedTypes('array', 'callable')
            ->info('Extra HTML attributes to render on the column value cell.')
        ;

        $resolver
            ->define('priority')
            ->default(0)
            ->allowedTypes('int')
            ->info(
                'Defines the priority of the column - the higher the priority, the earlier the column will be rendered.',
            )
        ;

        $resolver
            ->define('visible')
            ->default(true)
            ->allowedTypes('bool')
            ->info('Defines the visibility of the column.')
        ;

        $resolver
            ->define('personalizable')
            ->default(true)
            ->allowedTypes('bool')
            ->info('Defines whether the column can be personalized by the user in personalization feature.')
        ;

        $resolver
            ->define('column_visibility_groups')
            ->default([])
            ->allowedTypes('string[]')
            ->info('Defines the column visibility groups that show the column. Empty shows it in every group.')
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
     * @param array<array-key, mixed> $options
     */
    private function getPropertyPath(ColumnBuilderInterface $builder, array $options): string|PropertyPathInterface|null
    {
        /** @var bool|string|PropertyPathInterface $propertyPath */
        $propertyPath = $options['property_path'] ?? $builder->getName();

        if (is_bool($propertyPath)) {
            return null;
        }

        if ($propertyPath === '') {
            return null;
        }

        return $propertyPath;
    }

    private function getSortPropertyPath(ColumnBuilderInterface $builder, bool|string $sort): ?string
    {
        if ($sort === true) {
            return $builder->getName();
        }

        if (is_string($sort)) {
            return $sort;
        }

        return null;
    }

    /**
     * @param array<array-key, mixed> $options
     *
     * @return array<array-key, mixed>|null
     */
    private function getExportOptions(array $options): ?array
    {
        /** @var bool|array<array-key, mixed> $export */
        $export = $options['export'];

        if ($export === false) {
            return null;
        }

        if ($export === true) {
            return [];
        }

        return $export;
    }

    /**
     * @param array<array-key, mixed> $translationParameters
     */
    private function translateExportLabel(
        string|TranslatableInterface $label,
        bool|string|null $translationDomain,
        array $translationParameters,
    ): string|TranslatableInterface {
        $translator = $this->translator;

        if ($translator === null) {
            return $label;
        }

        if ($label instanceof TranslatableInterface) {
            return $label->trans($translator);
        }

        if (!is_string($translationDomain)) {
            return $label;
        }

        if ($translationDomain === '') {
            return $label;
        }

        return $translator->trans($label, $translationParameters, $translationDomain);
    }

    /**
     * @param array<array-key, mixed> $exportOptions
     */
    private function translateExportValue(mixed $value, mixed $data, mixed $rowData, array $exportOptions): mixed
    {
        $translator = $this->translator;

        if ($translator === null) {
            return $value;
        }

        if ($value instanceof TranslatableInterface) {
            return $value->trans($translator);
        }

        /** @var bool|string|null $translationDomain */
        $translationDomain = $exportOptions['value_translation_domain'];

        if (!is_string($translationDomain)) {
            return $value;
        }

        if ($translationDomain === '') {
            return $value;
        }

        /** @var string|null $translationKey */
        $translationKey = $exportOptions['value_translation_key'];

        if (is_string($value)) {
            $translationKey ??= $value;
        }

        if ($translationKey === null) {
            return $value;
        }

        /** @var array<array-key, mixed>|callable $translationParameters */
        $translationParameters = $exportOptions['value_translation_parameters'];

        return $translator->trans(
            $translationKey,
            $this->resolveRowDataOption($translationParameters, $data, $rowData),
            $translationDomain,
        );
    }

    /**
     * @param array<array-key, mixed>|callable $option
     *
     * @return array<array-key, mixed>
     */
    private function resolveRowDataOption(array|callable $option, mixed $data, mixed $rowData): array
    {
        if (!is_callable($option)) {
            return $option;
        }

        /** @var array<array-key, mixed>|null $resolvedOption */
        $resolvedOption = $option($data, $rowData);

        return $resolvedOption ?? [];
    }

    /**
     * @param array<array-key, mixed> $options
     */
    private function getColumnDataFromRowData(mixed $rowData, ColumnInterface $column, array $options): mixed
    {
        if ($rowData === null) {
            return null;
        }

        /** @var callable|false|null $getter */
        $getter = $options['getter'];

        if (is_callable($getter)) {
            return $getter($rowData, $column, $options);
        }

        /** @var bool|string|PropertyPathInterface $propertyPath */
        $propertyPath = $options['property_path'] ?? $column->getName();

        if (
            (is_string($propertyPath) || $propertyPath instanceof PropertyPathInterface)
            && (is_array($rowData) || is_object($rowData))
        ) {
            /** @var PropertyAccessorInterface $propertyAccessor */
            $propertyAccessor = $options['property_accessor'];

            return $propertyAccessor->getValue($rowData, $propertyPath);
        }

        return $rowData;
    }

    /**
     * @param array<array-key, mixed> $options
     */
    private function getColumnValueFromColumnData(
        mixed $data,
        mixed $rowData,
        ColumnInterface $column,
        array $options,
    ): mixed {
        if ($data === null) {
            return null;
        }

        /** @var callable|false|null $formatter */
        $formatter = $options['formatter'];

        if (!is_callable($formatter)) {
            return $data;
        }

        return $formatter($data, $rowData, $column, $options);
    }

    /**
     * @param array<array-key, mixed> $options
     *
     * @return array<string>
     */
    private function getColumnBlockPrefixes(ColumnInterface $column, array $options): array
    {
        $type = $column->getConfig()->getType();

        $blockPrefixes = [
            $type->getBlockPrefix(),
        ];

        $parentType = $type->getParent();

        while ($parentType !== null) {
            $blockPrefixes[] = $parentType->getBlockPrefix();
            $parentType = $parentType->getParent();
        }

        /** @var string $blockPrefix */
        $blockPrefix = $options['block_prefix'] ?? '';

        if ($blockPrefix !== '') {
            array_unshift($blockPrefixes, $blockPrefix);
        }

        return array_unique($blockPrefixes);
    }
}

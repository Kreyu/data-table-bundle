<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableConfigInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectProviderInterface;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\RowIterator;
use Kreyu\Bundle\DataTableBundle\Util\FormUtil;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Contracts\Translation\TranslatableInterface;

final class DataTableType implements DataTableTypeInterface
{
    public function __construct(
        private readonly array $defaults = [],
    ) {}

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        /** @var array<string, \Closure> $setters */
        $setters = [
            'themes' => $builder->setThemes(...),
            'column_factory' => $builder->setColumnFactory(...),
            'filter_factory' => $builder->setFilterFactory(...),
            'action_factory' => $builder->setActionFactory(...),
            'exporter_factory' => $builder->setExporterFactory(...),
            'personalization_enabled' => $builder->setPersonalizationEnabled(...),
            'personalization_persistence_enabled' => $builder->setPersonalizationPersistenceEnabled(...),
            'personalization_persistence_adapter' => $builder->setPersonalizationPersistenceAdapter(...),
            'personalization_persistence_subject_provider' =>
                $builder->setPersonalizationPersistenceSubjectProvider(...),
            'personalization_form_factory' => $builder->setPersonalizationFormFactory(...),
            'column_visibility_groups' => $builder->setColumnVisibilityGroups(...),
            'filtration_enabled' => $builder->setFiltrationEnabled(...),
            'filtration_persistence_enabled' => $builder->setFiltrationPersistenceEnabled(...),
            'filtration_persistence_adapter' => $builder->setFiltrationPersistenceAdapter(...),
            'filtration_persistence_subject_provider' => $builder->setFiltrationPersistenceSubjectProvider(...),
            'filtration_form_factory' => $builder->setFiltrationFormFactory(...),
            'sorting_enabled' => $builder->setSortingEnabled(...),
            'sorting_persistence_enabled' => $builder->setSortingPersistenceEnabled(...),
            'sorting_persistence_adapter' => $builder->setSortingPersistenceAdapter(...),
            'sorting_persistence_subject_provider' => $builder->setSortingPersistenceSubjectProvider(...),
            'sorting_clearable' => $builder->setSortingClearable(...),
            'pagination_enabled' => $builder->setPaginationEnabled(...),
            'pagination_persistence_enabled' => $builder->setPaginationPersistenceEnabled(...),
            'pagination_persistence_adapter' => $builder->setPaginationPersistenceAdapter(...),
            'pagination_persistence_subject_provider' => $builder->setPaginationPersistenceSubjectProvider(...),
            'exporting_enabled' => $builder->setExportingEnabled(...),
            'exporting_form_factory' => $builder->setExportFormFactory(...),
            'request_handler' => $builder->setRequestHandler(...),
            'pagination_factory' => $builder->setPaginationFactory(...),
            'group_by' => $builder->setGroupBy(...),
        ];

        foreach ($setters as $option => $setter) {
            if ($options[$option] === null) {
                continue;
            }

            $setter($options[$option]);
        }
    }

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
        $visibleColumns = $dataTable->getVisibleColumns();
        $deferred = $dataTable->isDeferred();

        $view->vars = array_replace(
            $view->vars,
            $this->getOptionViewVars($options),
            $this->getConfigViewVars($dataTable->getConfig()),
            $this->getStateViewVars($view, $dataTable),
        );

        $headerRow = $this->createHeaderRowView($view, $dataTable, $visibleColumns);

        $view->headerRow = $headerRow;
        $view->nonPersonalizedHeaderRow = $this->createHeaderRowView($view, $dataTable, $dataTable->getColumns());
        $view->valueRows = $deferred
            ? []
            : new RowIterator(fn () => $this->createGroupedValueRowsViews($view, $dataTable, $visibleColumns));
        $view->pagination = $deferred ? null : $this->createPaginationView($view, $dataTable);
        $view->filters = $this->createFilterViews($view, $dataTable);
        $view->actions = $this->createActionViews($view, $dataTable);

        $view->vars = array_replace($view->vars, [
            'header_row' => $headerRow,
            'value_rows' => $view->valueRows,
            'pagination' => $view->pagination,
            'filters' => $view->filters,
            'actions' => $view->actions,
            'batch_actions' => $this->createBatchActionViews($view, $dataTable),
            'column_count' => count($headerRow),
        ]);

        if ($deferred) {
            $view->vars['url_query_parameters'] = [];

            return;
        }

        $view->vars = array_replace($view->vars, $this->createFormViews($view, $dataTable));
        $view->vars['url_query_parameters'] = $this->getUrlQueryParameters($view, $dataTable);
    }

    public function buildExportView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
        $view->vars['translation_domain'] = $dataTable->getConfig()->getOption('translation_domain');

        $columns = $dataTable->getExportableColumns();

        $view->headerRow = $this->createExportHeaderRowView($view, $dataTable, $columns);
        $view->valueRows = new RowIterator(fn () => $this->createExportValueRowsViews($view, $dataTable, $columns));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $this->configureGeneralOptions($resolver);
        $this->configureSortingOptions($resolver);
        $this->configurePaginationOptions($resolver);
        $this->configureFiltrationOptions($resolver);
        $this->configurePersonalizationOptions($resolver);
        $this->configureExportingOptions($resolver);
    }

    public function createDefaultQuery(array $options): mixed
    {
        return null;
    }

    public function getName(): string
    {
        return 'data_table';
    }

    public function getParent(): ?string
    {
        return null;
    }

    private function configureGeneralOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'title' => null,
                'title_translation_parameters' => [],
                'translation_domain' => null,
                'themes' => $this->defaults['themes'] ?? [],
                'column_factory' => $this->defaults['column_factory'] ?? null,
                'action_factory' => $this->defaults['action_factory'] ?? null,
                'request_handler' => $this->defaults['request_handler'] ?? null,
                'column_visibility_groups' => [],
                'async' => $this->defaults['async'] ?? false,
                'async_loading' => $this->defaults['async_loading'] ?? 'lazy',
                'group_by' => null,
            ])
            ->setAllowedTypes('title', ['null', 'string', TranslatableInterface::class])
            ->setAllowedTypes('title_translation_parameters', ['array'])
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('themes', ['null', 'string[]'])
            ->setAllowedTypes('column_factory', ['null', ColumnFactoryInterface::class])
            ->setAllowedTypes('action_factory', ['null', ActionFactoryInterface::class])
            ->setAllowedTypes('request_handler', ['null', RequestHandlerInterface::class])
            ->setAllowedTypes('column_visibility_groups', 'array')
            ->setAllowedValues('column_visibility_groups', $this->areColumnVisibilityGroupsValid(...))
            ->setAllowedTypes('async', 'bool')
            ->setAllowedTypes('async_loading', 'string')
            ->setAllowedValues('async_loading', ['lazy', 'eager'])
            ->setAllowedTypes('group_by', ['null', 'string', 'callable'])
        ;
    }

    private function configureSortingOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'sorting_enabled' => $this->defaults['sorting']['enabled'] ?? true,
                'sorting_clearable' => $this->defaults['sorting']['clearable'] ?? true,
                'sorting_persistence_enabled' => $this->defaults['sorting']['persistence_enabled'] ?? false,
                'sorting_persistence_adapter' => $this->defaults['sorting']['persistence_adapter'] ?? null,
                'sorting_persistence_subject_provider' =>
                    $this->defaults['sorting']['persistence_subject_provider'] ?? null,
            ])
            ->setAllowedTypes('sorting_enabled', 'bool')
            ->setAllowedTypes('sorting_clearable', 'bool')
            ->setAllowedTypes('sorting_persistence_enabled', 'bool')
            ->setAllowedTypes('sorting_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('sorting_persistence_subject_provider', [
                'null',
                PersistenceSubjectProviderInterface::class,
            ])
        ;
    }

    private function configurePaginationOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'pagination_enabled' => $this->defaults['pagination']['enabled'] ?? true,
                'pagination_persistence_enabled' => $this->defaults['pagination']['persistence_enabled'] ?? false,
                'pagination_persistence_adapter' => $this->defaults['pagination']['persistence_adapter'] ?? null,
                'pagination_persistence_subject_provider' =>
                    $this->defaults['pagination']['persistence_subject_provider'] ?? null,
                'per_page_choices' => $this->defaults['pagination']['per_page_choices'] ?? [10, 25, 50, 100],
                'pagination_factory' => $this->defaults['pagination']['pagination_factory'] ?? null,
                'page_visible_range' => $this->defaults['pagination']['page_visible_range'] ?? 3,
            ])
            ->setAllowedTypes('pagination_enabled', 'bool')
            ->setAllowedTypes('pagination_persistence_enabled', 'bool')
            ->setAllowedTypes('pagination_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('pagination_persistence_subject_provider', [
                'null',
                PersistenceSubjectProviderInterface::class,
            ])
            ->setAllowedTypes('pagination_factory', ['null', PaginationFactoryInterface::class])
            ->setAllowedTypes('page_visible_range', 'int')
            ->setAllowedValues('page_visible_range', static fn (int $value): bool => $value >= 0)
        ;
    }

    private function configureFiltrationOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'filter_factory' => $this->defaults['filtration']['filter_factory'] ?? null,
                'filtration_enabled' => $this->defaults['filtration']['enabled'] ?? false,
                'filtration_persistence_enabled' => $this->defaults['filtration']['persistence_enabled'] ?? false,
                'filtration_persistence_adapter' => $this->defaults['filtration']['persistence_adapter'] ?? null,
                'filtration_persistence_subject_provider' =>
                    $this->defaults['filtration']['persistence_subject_provider'] ?? null,
                'filtration_form_factory' => $this->defaults['filtration']['form_factory'] ?? null,
            ])
            ->setAllowedTypes('filter_factory', ['null', FilterFactoryInterface::class])
            ->setAllowedTypes('filtration_enabled', 'bool')
            ->setAllowedTypes('filtration_persistence_enabled', 'bool')
            ->setAllowedTypes('filtration_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('filtration_persistence_subject_provider', [
                'null',
                PersistenceSubjectProviderInterface::class,
            ])
            ->setAllowedTypes('filtration_form_factory', ['null', FormFactoryInterface::class])
        ;
    }

    private function configurePersonalizationOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'personalization_enabled' => $this->defaults['personalization']['enabled'] ?? false,
                'personalization_persistence_enabled' =>
                    $this->defaults['personalization']['persistence_enabled'] ?? false,
                'personalization_persistence_adapter' =>
                    $this->defaults['personalization']['persistence_adapter'] ?? null,
                'personalization_persistence_subject_provider' =>
                    $this->defaults['personalization']['persistence_subject_provider'] ?? null,
                'personalization_form_factory' => $this->defaults['personalization']['form_factory'] ?? null,
            ])
            ->setAllowedTypes('personalization_enabled', 'bool')
            ->setAllowedTypes('personalization_persistence_enabled', 'bool')
            ->setAllowedTypes('personalization_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('personalization_persistence_subject_provider', [
                'null',
                PersistenceSubjectProviderInterface::class,
            ])
            ->setAllowedTypes('personalization_form_factory', ['null', FormFactoryInterface::class])
        ;
    }

    private function configureExportingOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'exporter_factory' => $this->defaults['exporting']['exporter_factory'] ?? null,
                'exporting_enabled' => $this->defaults['exporting']['enabled'] ?? false,
                'exporting_form_factory' => $this->defaults['exporting']['form_factory'] ?? null,
            ])
            ->setAllowedTypes('exporter_factory', ['null', ExporterFactoryInterface::class])
            ->setAllowedTypes('exporting_enabled', 'bool')
            ->setAllowedTypes('exporting_form_factory', ['null', FormFactoryInterface::class])
        ;
    }

    private function areColumnVisibilityGroupsValid(array $columnVisibilityGroups): bool
    {
        foreach (array_keys($columnVisibilityGroups) as $name) {
            $nameValid = is_string($name);

            if (!$nameValid) {
                return false;
            }

            $isStringLabel = is_string($columnVisibilityGroups[$name]);
            $isTranslatableLabel = $columnVisibilityGroups[$name] instanceof TranslatableInterface;
            $labelValid = $isStringLabel || $isTranslatableLabel;

            if (!$labelValid) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<string, mixed>
     */
    private function getOptionViewVars(array $options): array
    {
        return [
            'is_async' => $options['async'],
            'async_loading' => $options['async_loading'],
            'title' => $options['title'],
            'title_translation_parameters' => $options['title_translation_parameters'],
            'translation_domain' => $options['translation_domain'],
            'per_page_choices' => $options['per_page_choices'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getConfigViewVars(DataTableConfigInterface $config): array
    {
        return [
            'themes' => $config->getThemes(),
            'name' => $config->getName(),
            'pagination_enabled' => $config->isPaginationEnabled(),
            'sorting_enabled' => $config->isSortingEnabled(),
            'sorting_clearable' => $config->isSortingClearable(),
            'filtration_enabled' => $config->isFiltrationEnabled(),
            'exporting_enabled' => $config->isExportingEnabled(),
            'personalization_enabled' => $config->isPersonalizationEnabled(),
            'page_parameter_name' => $config->getPageParameterName(),
            'per_page_parameter_name' => $config->getPerPageParameterName(),
            'sort_parameter_name' => $config->getSortParameterName(),
            'filtration_parameter_name' => $config->getFiltrationParameterName(),
            'personalization_parameter_name' => $config->getPersonalizationParameterName(),
            'export_parameter_name' => $config->getExportParameterName(),
            'column_visibility_group_parameter_name' => $config->getColumnVisibilityGroupParameterName(),
            'column_visibility_groups' => $config->getColumnVisibilityGroups(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function getStateViewVars(DataTableView $view, DataTableInterface $dataTable): array
    {
        return [
            'data_table' => $view,
            'async_deferred' => $dataTable->isDeferred(),
            'exporters' => $dataTable->getExporters(),
            'column_visibility_group' => $dataTable->getColumnVisibilityGroup(),
            'has_active_filters' => $dataTable->hasActiveFilters(),
            'has_active_non_search_filters' => $this->hasActiveNonSearchFilters($dataTable),
            'filtration_data' => $dataTable->getFiltrationData(),
            'sorting_data' => $dataTable->getSortingData(),
            'has_batch_actions' => $dataTable->getBatchActions() !== [],
        ];
    }

    private function createPaginationView(DataTableView $view, DataTableInterface $dataTable): ?PaginationView
    {
        if (!$dataTable->getConfig()->isPaginationEnabled()) {
            return null;
        }

        return new PaginationView($view, $dataTable->getPagination());
    }

    /**
     * @return array<ActionView>
     */
    private function createActionViews(DataTableView $view, DataTableInterface $dataTable): array
    {
        return array_map(
            static fn (ActionInterface $action) => $action->createView($view),
            $dataTable->getActions(),
        );
    }

    /**
     * @return array<ActionView>
     */
    private function createBatchActionViews(DataTableView $view, DataTableInterface $dataTable): array
    {
        return array_map(
            static fn (ActionInterface $action) => $action->createView($view),
            $dataTable->getBatchActions(),
        );
    }

    private function hasActiveNonSearchFilters(DataTableInterface $dataTable): bool
    {
        $filtrationData = $dataTable->getFiltrationData();

        if ($filtrationData === null) {
            return false;
        }

        $nonPanelFilterNames = [];

        foreach ($dataTable->getFilters() as $name => $filter) {
            $innerType = $filter->getConfig()->getType()->getInnerType();

            if ($innerType instanceof SearchFilterTypeInterface) {
                $nonPanelFilterNames[] = $name;
            }
        }

        foreach ($dataTable->getVisibleColumns() as $name => $column) {
            $hasHeaderFilter = $column->getConfig()->getOption('filter') !== null;

            if ($hasHeaderFilter) {
                $nonPanelFilterNames[] = $name;
            }
        }

        return $filtrationData->hasActiveFiltersExcept($nonPanelFilterNames);
    }

    /**
     * @return array<FilterView>
     */
    private function createFilterViews(DataTableView $view, DataTableInterface $dataTable): array
    {
        if (!$dataTable->getConfig()->isFiltrationEnabled()) {
            return [];
        }

        return array_map(
            static fn (FilterInterface $filter) => $filter->createView(
                $dataTable->getFiltrationData()?->getFilterData($filter) ?? new FilterData(),
                $view,
            ),
            $dataTable->getFilters(),
        );
    }

    /**
     * @param array<ColumnInterface> $columns
     */
    private function createHeaderRowView(
        DataTableView $view,
        DataTableInterface $dataTable,
        array $columns,
    ): HeaderRowView {
        $headerRowView = new HeaderRowView($view);
        $headerRowView->vars['row'] = $headerRowView;
        $headerRowView->vars['attr'] = $dataTable->getConfig()->getHeaderRowAttributes();

        foreach ($columns as $column) {
            $headerRowView->children[$column->getName()] = $column->createHeaderView($headerRowView);
        }

        $headerRowView->vars['children'] = $headerRowView->children;

        return $headerRowView;
    }

    /**
     * @param array<ColumnInterface> $columns
     *
     * @return \Generator<int, ValueRowView>
     */
    private function createValueRowsViews(
        DataTableView $view,
        DataTableInterface $dataTable,
        array $columns,
    ): \Generator {
        $items = $dataTable->getItems();

        /**
         * @var int $index
         * @var mixed $data
         */
        foreach ($items as $index => $data) {
            $valueRowView = new ValueRowView($view, $index, $data);
            $valueRowView->vars['row'] = $valueRowView;
            $valueRowView->vars['attr'] = $this->getValueRowAttributes($dataTable, $data);

            foreach ($columns as $column) {
                $valueRowView->children[$column->getName()] = $column->createValueView($valueRowView);
            }

            yield $valueRowView;
        }

        yield from [];
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getValueRowAttributes(DataTableInterface $dataTable, mixed $data): array
    {
        return array_map(
            static fn (mixed $value): mixed => is_callable($value) ? $value($data, $dataTable) : $value,
            $dataTable->getConfig()->getValueRowAttributes(),
        );
    }

    /**
     * @param array<ColumnInterface> $columns
     *
     * @return \Generator<int, ValueRowView>
     */
    private function createGroupedValueRowsViews(
        DataTableView $view,
        DataTableInterface $dataTable,
        array $columns,
    ): \Generator {
        $valueRowViews = $this->createValueRowsViews($view, $dataTable, $columns);
        $groupBy = $dataTable->getConfig()->getGroupBy();

        if ($groupBy === null) {
            yield from $valueRowViews;

            return;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $isFirstRow = true;
        $previousGroupValue = null;

        foreach ($valueRowViews as $valueRowView) {
            /** @var mixed $groupValue */
            $groupValue = $this->getGroupValue($valueRowView->data, $groupBy, $propertyAccessor);
            $isSameGroup = $this->isSameGroupValue($groupValue, $previousGroupValue);

            if ($isFirstRow || !$isSameGroup) {
                yield $this->createGroupHeaderRowView($view, $valueRowView->index, $groupValue);
            }

            yield $valueRowView;

            $isFirstRow = false;

            /** @var mixed $previousGroupValue */
            $previousGroupValue = $groupValue;
        }
    }

    private function isSameGroupValue(mixed $groupValue, mixed $previousGroupValue): bool
    {
        $isDateGroup = $groupValue instanceof \DateTimeInterface && $previousGroupValue instanceof \DateTimeInterface;

        if ($isDateGroup) {
            return $groupValue->format('U.u') === $previousGroupValue->format('U.u');
        }

        return $groupValue === $previousGroupValue;
    }

    private function getGroupValue(
        mixed $data,
        string|callable $groupBy,
        PropertyAccessorInterface $propertyAccessor,
    ): mixed {
        $isPropertyPath = is_string($groupBy);

        if (!$isPropertyPath) {
            return $groupBy($data);
        }

        $isReadable = is_array($data) || is_object($data);

        if (!$isReadable) {
            return null;
        }

        return $propertyAccessor->getValue($data, $groupBy);
    }

    private function createGroupHeaderRowView(DataTableView $view, int $index, mixed $groupValue): ValueRowView
    {
        $groupHeaderRowView = new ValueRowView($view, $index, null);
        $groupHeaderRowView->vars['row'] = $groupHeaderRowView;
        $groupHeaderRowView->vars['row_type'] = 'group_header';
        $groupHeaderRowView->vars['group_value'] = $groupValue;

        return $groupHeaderRowView;
    }

    /**
     * @param array<ColumnInterface> $columns
     */
    private function createExportHeaderRowView(
        DataTableView $view,
        DataTableInterface $dataTable,
        array $columns,
    ): HeaderRowView {
        $headerRowView = new HeaderRowView($view);

        foreach ($columns as $column) {
            $isExportable = $column->getConfig()->isExportable();

            if (!$isExportable) {
                continue;
            }

            $headerRowView->children[$column->getName()] = $column->createExportHeaderView($headerRowView);
        }

        return $headerRowView;
    }

    /**
     * @param array<ColumnInterface> $columns
     *
     * @return \Generator<int, ValueRowView>
     */
    private function createExportValueRowsViews(
        DataTableView $view,
        DataTableInterface $dataTable,
        array $columns,
    ): \Generator {
        $items = $dataTable->getItems();

        /**
         * @var int $index
         * @var mixed $data
         */
        foreach ($items as $index => $data) {
            $valueRowView = new ValueRowView($view, $index, $data);

            foreach ($columns as $column) {
                $valueRowView->children[$column->getName()] = $column->createExportValueView($valueRowView);
            }

            yield $valueRowView;
        }

        yield from [];
    }

    /**
     * @return array<string, FormView>
     */
    private function createFormViews(DataTableView $view, DataTableInterface $dataTable): array
    {
        $config = $dataTable->getConfig();
        $isFiltrationEnabled = $config->isFiltrationEnabled();
        $isPersonalizationEnabled = $config->isPersonalizationEnabled();
        $isExportingEnabled = $config->isExportingEnabled();
        $formViews = [];

        if ($isFiltrationEnabled) {
            $formViews['filtration_form'] = $this->createFiltrationFormView($view, $dataTable);
        }

        if ($isPersonalizationEnabled) {
            $formViews['personalization_form'] = $this->createPersonalizationFormView($view, $dataTable);
        }

        if ($isExportingEnabled) {
            $formViews['export_form'] = $this->createExportFormView($view, $dataTable);
        }

        return $formViews;
    }

    private function createFiltrationFormView(DataTableView $view, DataTableInterface $dataTable): FormView
    {
        $form = $dataTable->createFiltrationFormBuilder($view)->getForm();
        $form->setData($dataTable->getFiltrationData());

        return $this->createFormView($form, $view, $dataTable);
    }

    private function createPersonalizationFormView(DataTableView $view, DataTableInterface $dataTable): FormView
    {
        $form = $dataTable->createPersonalizationFormBuilder($view)->getForm();
        $form->setData($dataTable->getPersonalizationData());

        return $this->createFormView($form, $view, $dataTable);
    }

    private function createExportFormView(DataTableView $view, DataTableInterface $dataTable): FormView
    {
        $form = $dataTable->createExportFormBuilder()->getForm();
        $form->setData($dataTable->getExportData());

        return $this->createFormView($form, $view, $dataTable);
    }

    private function createFormView(FormInterface $form, DataTableView $view, DataTableInterface $dataTable): FormView
    {
        $formView = $form->createView();
        $formView->vars['data_table'] = $dataTable;
        $formView->vars['data_table_view'] = $view;

        return $formView;
    }

    /**
     * @return array<string, mixed>
     */
    private function getUrlQueryParameters(DataTableView $view, DataTableInterface $dataTable): array
    {
        $config = $dataTable->getConfig();
        $parameters = [];

        $filtrationParameters = $this->getFiltrationQueryParameters($view, $dataTable);

        if ($filtrationParameters !== []) {
            $parameters[$config->getFiltrationParameterName()] = $filtrationParameters;
        }

        $isPaginationEnabled = $config->isPaginationEnabled();

        if ($isPaginationEnabled) {
            $pagination = $dataTable->getPagination();

            $parameters[$config->getPageParameterName()] = $pagination->getCurrentPageNumber();
            $parameters[$config->getPerPageParameterName()] = $pagination->getItemNumberPerPage();
        }

        $sortingParameters = $this->getSortingQueryParameters($dataTable);

        if ($sortingParameters !== []) {
            $parameters[$config->getSortParameterName()] = $sortingParameters;
        }

        $columnVisibilityGroup = $dataTable->getColumnVisibilityGroup();

        if ($columnVisibilityGroup !== null) {
            $parameters[$config->getColumnVisibilityGroupParameterName()] = $columnVisibilityGroup;
        }

        return $parameters;
    }

    /**
     * @return array<string, array{value: mixed, operator?: string|null}>
     */
    private function getFiltrationQueryParameters(DataTableView $view, DataTableInterface $dataTable): array
    {
        $isFiltrationEnabled = $dataTable->getConfig()->isFiltrationEnabled();

        if (!$isFiltrationEnabled) {
            return [];
        }

        /** @var FormView $filtrationFormView */
        $filtrationFormView = $view->vars['filtration_form'];

        /** @var array<string, FormView> $searchFields */
        $searchFields = $filtrationFormView->vars['search_fields'];

        /** @var array<string, FormView> $headerFields */
        $headerFields = $filtrationFormView->vars['header_fields'];

        $parameters = [];

        foreach ($view->filters as $filterView) {
            /** @var string $formName */
            $formName = $filterView->vars['form_name'];
            $panelFormView = $filtrationFormView[$formName] ?? null;
            $searchFormView = $searchFields[$formName] ?? null;
            $headerFormView = $headerFields[$formName] ?? null;
            $filterFormView = $panelFormView ?? $searchFormView ?? $headerFormView;
            $filterParameter = $this->getFilterQueryParameter($filterView, $filterFormView);

            if ($filterParameter === null) {
                continue;
            }

            $parameters[$formName] = $filterParameter;
        }

        return $parameters;
    }

    /**
     * @return array{value: mixed, operator?: string|null}|null
     */
    private function getFilterQueryParameter(FilterView $filterView, ?FormView $filterFormView): ?array
    {
        $valueFormView = $filterFormView['value'] ?? null;

        if ($valueFormView === null) {
            return null;
        }

        /** @var mixed $value */
        $value = FormUtil::getFormViewValueRecursive($valueFormView);
        $isEmptyValue = in_array($value, [null, '', []], strict: true);

        if ($isEmptyValue) {
            return null;
        }

        $filterParameter = ['value' => $value];

        if ($filterView->vars['operator_selectable']) {
            $filterParameter['operator'] = $filterView->data?->getOperator()?->value;
        }

        return $filterParameter;
    }

    /**
     * @return array<string, string>
     */
    private function getSortingQueryParameters(DataTableInterface $dataTable): array
    {
        $isSortingEnabled = $dataTable->getConfig()->isSortingEnabled();

        if (!$isSortingEnabled) {
            return [];
        }

        $sortingData = $dataTable->getSortingData();

        if ($sortingData === null) {
            return [];
        }

        $parameters = [];

        foreach ($sortingData->getColumns() as $sortingColumnData) {
            $parameters[$sortingColumnData->getName()] = $sortingColumnData->getDirection();
        }

        return $parameters;
    }
}

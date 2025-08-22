<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\ColumnVisibilityGroup\ColumnVisibilityGroupBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
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
use Symfony\Contracts\Translation\TranslatableInterface;

final class DataTableType implements DataTableTypeInterface
{
    public function __construct(
        private readonly array $defaults = [],
    ) {
    }

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $setters = [
            'themes' => $builder->setThemes(...),
            'column_factory' => $builder->setColumnFactory(...),
            'column_visibility_group_builder' => $builder->setColumnVisibilityGroupBuilder(...),
            'filter_factory' => $builder->setFilterFactory(...),
            'action_factory' => $builder->setActionFactory(...),
            'exporter_factory' => $builder->setExporterFactory(...),
            'personalization_enabled' => $builder->setPersonalizationEnabled(...),
            'personalization_persistence_enabled' => $builder->setPersonalizationPersistenceEnabled(...),
            'personalization_persistence_adapter' => $builder->setPersonalizationPersistenceAdapter(...),
            'personalization_persistence_subject_provider' => $builder->setPersonalizationPersistenceSubjectProvider(...),
            'personalization_form_factory' => $builder->setPersonalizationFormFactory(...),
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
        ];

        foreach ($setters as $option => $setter) {
            if (null !== $value = $options[$option]) {
                $setter($value);
            }
        }
    }

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
        $columns = $dataTable->getColumns();
        $visibleColumns = $dataTable->getVisibleColumns();

        $view->vars = array_replace($view->vars, [
            'data_table' => $view,
            'themes' => $dataTable->getConfig()->getThemes(),
            'title' => $options['title'],
            'title_translation_parameters' => $options['title_translation_parameters'],
            'translation_domain' => $options['translation_domain'],
            'name' => $dataTable->getConfig()->getName(),
            'exporters' => $dataTable->getExporters(),
            'pagination_enabled' => $dataTable->getConfig()->isPaginationEnabled(),
            'sorting_enabled' => $dataTable->getConfig()->isSortingEnabled(),
            'filtration_enabled' => $dataTable->getConfig()->isFiltrationEnabled(),
            'exporting_enabled' => $dataTable->getConfig()->isExportingEnabled(),
            'personalization_enabled' => $dataTable->getConfig()->isPersonalizationEnabled(),
            'page_parameter_name' => $dataTable->getConfig()->getPageParameterName(),
            'per_page_parameter_name' => $dataTable->getConfig()->getPerPageParameterName(),
            'sort_parameter_name' => $dataTable->getConfig()->getSortParameterName(),
            'filtration_parameter_name' => $dataTable->getConfig()->getFiltrationParameterName(),
            'personalization_parameter_name' => $dataTable->getConfig()->getPersonalizationParameterName(),
            'export_parameter_name' => $dataTable->getConfig()->getExportParameterName(),
            'has_active_filters' => $dataTable->hasActiveFilters(),
            'filtration_data' => $dataTable->getFiltrationData(),
            'sorting_data' => $dataTable->getSortingData(),
            'sorting_clearable' => $dataTable->getConfig()->isSortingClearable(),
            'has_batch_actions' => !empty($dataTable->getBatchActions()),
            'per_page_choices' => $options['per_page_choices'],
        ]);

        $view->headerRow = $this->createHeaderRowView($view, $dataTable, $visibleColumns);
        $view->nonPersonalizedHeaderRow = $this->createHeaderRowView($view, $dataTable, $columns);
        $view->valueRows = new RowIterator(fn () => $this->createValueRowsViews($view, $dataTable, $visibleColumns));
        $view->pagination = $this->createPaginationView($view, $dataTable);
        $view->filters = $this->createFilterViews($view, $dataTable);
        $view->actions = $this->createActionViews($view, $dataTable);

        $view->vars = array_replace($view->vars, [
            'header_row' => $view->headerRow,
            'value_rows' => $view->valueRows,
            'pagination' => $view->pagination,
            'filters' => $view->filters,
            'actions' => $view->actions,
            'batch_actions' => $this->createBatchActionViews($view, $dataTable),
            'column_count' => count($view->headerRow),
        ]);

        if ($dataTable->getConfig()->isFiltrationEnabled()) {
            $view->vars['filtration_form'] = $this->createFiltrationFormView($view, $dataTable);
        }

        if ($dataTable->getConfig()->isPersonalizationEnabled()) {
            $view->vars['personalization_form'] = $this->createPersonalizationFormView($view, $dataTable);
        }

        if ($dataTable->getConfig()->isExportingEnabled()) {
            $view->vars['export_form'] = $this->createExportFormView($view, $dataTable);
        }

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
        $resolver
            ->setDefaults([
                'title' => null,
                'title_translation_parameters' => [],
                'translation_domain' => null,
                'themes' => $this->defaults['themes'] ?? [],
                'column_factory' => $this->defaults['column_factory'] ?? null,
                'filter_factory' => $this->defaults['filtration']['filter_factory'] ?? null,
                'action_factory' => $this->defaults['action_factory'] ?? null,
                'exporter_factory' => $this->defaults['exporting']['exporter_factory'] ?? null,
                'column_visibility_group_builder' => $this->defaults['column_visibility_group_builder'] ?? null,
                'request_handler' => $this->defaults['request_handler'] ?? null,
                'sorting_enabled' => $this->defaults['sorting']['enabled'] ?? true,
                'sorting_clearable' => $this->defaults['sorting']['clearable'] ?? true,
                'sorting_persistence_enabled' => $this->defaults['sorting']['persistence_enabled'] ?? false,
                'sorting_persistence_adapter' => $this->defaults['sorting']['persistence_adapter'] ?? null,
                'sorting_persistence_subject_provider' => $this->defaults['sorting']['persistence_subject_provider'] ?? null,
                'pagination_enabled' => $this->defaults['pagination']['enabled'] ?? true,
                'pagination_persistence_enabled' => $this->defaults['pagination']['persistence_enabled'] ?? false,
                'pagination_persistence_adapter' => $this->defaults['pagination']['persistence_adapter'] ?? null,
                'pagination_persistence_subject_provider' => $this->defaults['pagination']['persistence_subject_provider'] ?? null,
                'per_page_choices' => $this->defaults['pagination']['per_page_choices'] ?? [10, 25, 50, 100],
                'filtration_enabled' => $this->defaults['filtration']['enabled'] ?? false,
                'filtration_persistence_enabled' => $this->defaults['filtration']['persistence_enabled'] ?? false,
                'filtration_persistence_adapter' => $this->defaults['filtration']['persistence_adapter'] ?? null,
                'filtration_persistence_subject_provider' => $this->defaults['filtration']['persistence_subject_provider'] ?? null,
                'filtration_form_factory' => $this->defaults['filtration']['form_factory'] ?? null,
                'personalization_enabled' => $this->defaults['personalization']['enabled'] ?? false,
                'personalization_persistence_enabled' => $this->defaults['personalization']['persistence_enabled'] ?? false,
                'personalization_persistence_adapter' => $this->defaults['personalization']['persistence_adapter'] ?? null,
                'personalization_persistence_subject_provider' => $this->defaults['personalization']['persistence_subject_provider'] ?? null,
                'personalization_form_factory' => $this->defaults['personalization']['form_factory'] ?? null,
                'exporting_enabled' => $this->defaults['exporting']['enabled'] ?? false,
                'exporting_form_factory' => $this->defaults['exporting']['form_factory'] ?? null,
            ])
            ->setAllowedTypes('title', ['null', 'string', TranslatableInterface::class])
            ->setAllowedTypes('title_translation_parameters', ['array'])
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('themes', ['null', 'string[]'])
            ->setAllowedTypes('column_factory', ['null', ColumnFactoryInterface::class])
            ->setAllowedTypes('filter_factory', ['null', FilterFactoryInterface::class])
            ->setAllowedTypes('action_factory', ['null', ActionFactoryInterface::class])
            ->setAllowedTypes('exporter_factory', ['null', ExporterFactoryInterface::class])
            ->setAllowedTypes('column_visibility_group_builder', ['null', ColumnVisibilityGroupBuilderInterface::class])
            ->setAllowedTypes('request_handler', ['null', RequestHandlerInterface::class])
            ->setAllowedTypes('sorting_enabled', 'bool')
            ->setAllowedTypes('sorting_clearable', 'bool')
            ->setAllowedTypes('sorting_persistence_enabled', 'bool')
            ->setAllowedTypes('sorting_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('sorting_persistence_subject_provider', ['null', PersistenceSubjectProviderInterface::class])
            ->setAllowedTypes('pagination_enabled', 'bool')
            ->setAllowedTypes('pagination_persistence_enabled', 'bool')
            ->setAllowedTypes('pagination_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('pagination_persistence_subject_provider', ['null', PersistenceSubjectProviderInterface::class])
            ->setAllowedTypes('filtration_enabled', 'bool')
            ->setAllowedTypes('filtration_persistence_enabled', 'bool')
            ->setAllowedTypes('filtration_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('filtration_persistence_subject_provider', ['null', PersistenceSubjectProviderInterface::class])
            ->setAllowedTypes('filtration_form_factory', ['null', FormFactoryInterface::class])
            ->setAllowedTypes('personalization_enabled', 'bool')
            ->setAllowedTypes('personalization_persistence_enabled', 'bool')
            ->setAllowedTypes('personalization_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('personalization_persistence_subject_provider', ['null', PersistenceSubjectProviderInterface::class])
            ->setAllowedTypes('personalization_form_factory', ['null', FormFactoryInterface::class])
            ->setAllowedTypes('exporting_enabled', 'bool')
            ->setAllowedTypes('exporting_form_factory', ['null', FormFactoryInterface::class])
        ;
    }

    public function getName(): string
    {
        return 'data_table';
    }

    public function getParent(): ?string
    {
        return null;
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
    private function createHeaderRowView(DataTableView $view, DataTableInterface $dataTable, array $columns): HeaderRowView
    {
        $headerRowView = new HeaderRowView($view);
        $headerRowView->vars['row'] = $headerRowView;

        foreach ($dataTable->getConfig()->getHeaderRowAttributes() as $key => $value) {
            $headerRowView->vars['attr'][$key] = $value;
        }

        foreach ($columns as $column) {
            $headerRowView->children[$column->getName()] = $column->createHeaderView($headerRowView);
        }

        $headerRowView->vars['children'] = $headerRowView->children;

        return $headerRowView;
    }

    /**
     * @param array<ColumnInterface> $columns
     *
     * @return iterable<ValueRowView>
     */
    private function createValueRowsViews(DataTableView $view, DataTableInterface $dataTable, array $columns): iterable
    {
        $items = $dataTable->getItems();

        foreach ($items as $index => $data) {
            $valueRowView = new ValueRowView($view, $index, $data);
            $valueRowView->vars['row'] = $valueRowView;

            foreach ($dataTable->getConfig()->getValueRowAttributes() as $key => $value) {
                if (is_callable($value)) {
                    $value = $value($data, $dataTable);
                }

                $valueRowView->vars['attr'][$key] = $value;
            }

            foreach ($columns as $column) {
                $valueRowView->children[$column->getName()] = $column->createValueView($valueRowView);
            }

            yield $valueRowView;
        }

        yield from [];
    }

    /**
     * @param array<ColumnInterface> $columns
     */
    private function createExportHeaderRowView(DataTableView $view, DataTableInterface $dataTable, array $columns): HeaderRowView
    {
        $headerRowView = new HeaderRowView($view);

        foreach ($columns as $column) {
            if ($column->getConfig()->isExportable()) {
                $headerRowView->children[$column->getName()] = $column->createExportHeaderView($headerRowView);
            }
        }

        return $headerRowView;
    }

    /**
     * @param array<ColumnInterface> $columns
     */
    private function createExportValueRowsViews(DataTableView $view, DataTableInterface $dataTable, array $columns): iterable
    {
        $items = $dataTable->getItems();

        foreach ($items as $index => $data) {
            $valueRowView = new ValueRowView($view, $index, $data);

            foreach ($columns as $column) {
                $valueRowView->children[$column->getName()] = $column->createExportValueView($valueRowView);
            }

            yield $valueRowView;
        }

        yield from [];
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

    private function getUrlQueryParameters(DataTableView $view, DataTableInterface $dataTable): array
    {
        $parameters = [];

        if ($dataTable->getConfig()->isFiltrationEnabled()) {
            foreach ($view->filters as $filterView) {
                $value = null;

                if ($formView = $view->vars['filtration_form'][$filterView->vars['name']]['value'] ?? null) {
                    $value = FormUtil::getFormViewValueRecursive($formView);
                }

                if (null === $value || '' === $value || [] === $value) {
                    continue;
                }

                $filterParameter = ['value' => $value];

                if ($filterView->vars['operator_selectable']) {
                    $filterParameter['operator'] = $filterView->data->getOperator()?->value;
                }

                $parameters[$dataTable->getConfig()->getFiltrationParameterName()][$filterView->vars['name']] = $filterParameter;
            }
        }

        if ($dataTable->getConfig()->isPaginationEnabled()) {
            $parameters[$dataTable->getConfig()->getPageParameterName()] = $dataTable->getPagination()->getCurrentPageNumber();
            $parameters[$dataTable->getConfig()->getPerPageParameterName()] = $dataTable->getPagination()->getItemNumberPerPage();
        }

        if ($dataTable->getConfig()->isSortingEnabled() && $sortingData = $dataTable->getSortingData()) {
            foreach ($sortingData->getColumns() as $sortingColumnData) {
                $parameters[$dataTable->getConfig()->getSortParameterName()][$sortingColumnData->getName()] = $sortingColumnData->getDirection();
            }
        }

        return $parameters;
    }
}

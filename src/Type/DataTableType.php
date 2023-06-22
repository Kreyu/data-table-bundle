<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Form\Type\ExportDataType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceSubjectInterface;
use Kreyu\Bundle\DataTableBundle\Request\RequestHandlerInterface;
use Kreyu\Bundle\DataTableBundle\RowIterator;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

final class DataTableType implements DataTableTypeInterface
{
    public const DEFAULT_OPTIONS = [
        'themes' => null,
        'title' => null,
        'title_translation_parameters' => [],
        'translation_domain' => null,
        'column_factory' => null,
        'action_factory' => null,
        'request_handler' => null,
        'sorting_enabled' => null,
        'sorting_persistence_enabled' => null,
        'sorting_persistence_adapter' => null,
        'sorting_persistence_subject' => null,
        'pagination_enabled' => null,
        'pagination_persistence_enabled' => null,
        'pagination_persistence_adapter' => null,
        'pagination_persistence_subject' => null,
        'filtration_enabled' => null,
        'filtration_persistence_enabled' => null,
        'filtration_persistence_adapter' => null,
        'filtration_persistence_subject' => null,
        'filtration_form_factory' => null,
        'filter_factory' => null,
        'personalization_enabled' => null,
        'personalization_persistence_enabled' => null,
        'personalization_persistence_adapter' => null,
        'personalization_persistence_subject' => null,
        'personalization_form_factory' => null,
        'exporting_enabled' => null,
        'exporting_form_factory' => null,
        'exporter_factory' => null,
    ];

    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $setters = [
            'themes' => $builder->setThemes(...),
            'title' => $builder->setTitle(...),
            'title_translation_parameters' => $builder->setTitleTranslationParameters(...),
            'translation_domain' => $builder->setTranslationDomain(...),
            'column_factory' => $builder->setColumnFactory(...),
            'filter_factory' => $builder->setFilterFactory(...),
            'action_factory' => $builder->setActionFactory(...),
            'exporter_factory' => $builder->setExporterFactory(...),
            'personalization_enabled' => $builder->setPersonalizationEnabled(...),
            'personalization_persistence_enabled' => $builder->setPersonalizationPersistenceEnabled(...),
            'personalization_persistence_adapter' => $builder->setPersonalizationPersistenceAdapter(...),
            'personalization_persistence_subject' => $builder->setPersonalizationPersistenceSubject(...),
            'personalization_form_factory' => $builder->setPersonalizationFormFactory(...),
            'filtration_enabled' => $builder->setFiltrationEnabled(...),
            'filtration_persistence_enabled' => $builder->setFiltrationPersistenceEnabled(...),
            'filtration_persistence_adapter' => $builder->setFiltrationPersistenceAdapter(...),
            'filtration_persistence_subject' => $builder->setFiltrationPersistenceSubject(...),
            'filtration_form_factory' => $builder->setFiltrationFormFactory(...),
            'sorting_enabled' => $builder->setSortingEnabled(...),
            'sorting_persistence_enabled' => $builder->setSortingPersistenceEnabled(...),
            'sorting_persistence_adapter' => $builder->setSortingPersistenceAdapter(...),
            'sorting_persistence_subject' => $builder->setSortingPersistenceSubject(...),
            'pagination_enabled' => $builder->setPaginationEnabled(...),
            'pagination_persistence_enabled' => $builder->setPaginationPersistenceEnabled(...),
            'pagination_persistence_adapter' => $builder->setPaginationPersistenceAdapter(...),
            'pagination_persistence_subject' => $builder->setPaginationPersistenceSubject(...),
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
        $columns = $visibleColumns = $dataTable->getConfig()->getColumns();

        if ($dataTable->getConfig()->isPersonalizationEnabled()) {
            $visibleColumns = $dataTable->getPersonalizationData()->compute($columns);
        }

        $view->vars = array_replace($view->vars, [
            'themes' => $dataTable->getConfig()->getThemes(),
            'name' => $dataTable->getConfig()->getName(),
            'title' => $dataTable->getConfig()->getTitle(),
            'title_translation_parameters' => $dataTable->getConfig()->getTitleTranslationParameters(),
            'translation_domain' => $dataTable->getConfig()->getTranslationDomain(),
            'exporters' => $dataTable->getConfig()->getExporters(),
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults(self::DEFAULT_OPTIONS)
            ->setAllowedTypes('themes', ['null', 'string[]'])
            ->setAllowedTypes('title', ['null', 'string', TranslatableMessage::class])
            ->setAllowedTypes('title_translation_parameters', ['null', 'array'])
            ->setAllowedTypes('translation_domain', ['null', 'bool', 'string'])
            ->setAllowedTypes('column_factory', ['null', ColumnFactoryInterface::class])
            ->setAllowedTypes('action_factory', ['null', ActionFactoryInterface::class])
            ->setAllowedTypes('request_handler', ['null', RequestHandlerInterface::class])
            ->setAllowedTypes('sorting_enabled', ['null', 'bool'])
            ->setAllowedTypes('sorting_persistence_enabled', ['null', 'bool'])
            ->setAllowedTypes('sorting_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('sorting_persistence_subject', ['null', PersistenceSubjectInterface::class])
            ->setAllowedTypes('pagination_enabled', ['null', 'bool'])
            ->setAllowedTypes('pagination_persistence_enabled', ['null', 'bool'])
            ->setAllowedTypes('pagination_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('pagination_persistence_subject', ['null', PersistenceSubjectInterface::class])
            ->setAllowedTypes('filtration_enabled', ['null', 'bool'])
            ->setAllowedTypes('filtration_persistence_enabled', ['null', 'bool'])
            ->setAllowedTypes('filtration_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('filtration_persistence_subject', ['null', PersistenceSubjectInterface::class])
            ->setAllowedTypes('filtration_form_factory', ['null', FormFactoryInterface::class])
            ->setAllowedTypes('filter_factory', ['null', FilterFactoryInterface::class])
            ->setAllowedTypes('personalization_enabled', ['null', 'bool'])
            ->setAllowedTypes('personalization_persistence_enabled', ['null', 'bool'])
            ->setAllowedTypes('personalization_persistence_adapter', ['null', PersistenceAdapterInterface::class])
            ->setAllowedTypes('personalization_persistence_subject', ['null', PersistenceSubjectInterface::class])
            ->setAllowedTypes('personalization_form_factory', ['null', FormFactoryInterface::class])
            ->setAllowedTypes('exporting_enabled', ['null', 'bool'])
            ->setAllowedTypes('exporting_form_factory', ['null', FormFactoryInterface::class])
            ->setAllowedTypes('exporter_factory', ['null', ExporterFactoryInterface::class])
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

    private function createPaginationView(DataTableView $view, DataTableInterface $dataTable): PaginationView
    {
        return new PaginationView($view, $dataTable->getPagination());
    }

    /**
     * @return array<ActionView>
     */
    private function createActionViews(DataTableView $view, DataTableInterface $dataTable): array
    {
        return array_map(
            fn (ActionInterface $action) => $action->createView($view),
            $dataTable->getConfig()->getActions(),
        );
    }

    /**
     * @return array<FilterView>
     */
    private function createFilterViews(DataTableView $view, DataTableInterface $dataTable): array
    {
        $filters = [];

        if (!$dataTable->getConfig()->isFiltrationEnabled()) {
            return [];
        }

        foreach ($dataTable->getConfig()->getFilters() as $filter) {
            $data = $dataTable->getFiltrationData()->getFilterData($filter->getName()) ?? new FilterData();

            $filters[$filter->getName()] = $filter->createView($data, $view);
        }

        return $filters;
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

        return $headerRowView;
    }

    /**
     * @param  array<ColumnInterface> $columns
     * @return iterable<ValueRowView>
     */
    private function createValueRowsViews(DataTableView $view, DataTableInterface $dataTable, array $columns): iterable
    {
        foreach ($dataTable->getPagination()->getItems() as $index => $data) {
            $valueRowView = new ValueRowView($view, $index, $data);
            $valueRowView->vars['row'] = $valueRowView;

            foreach ($dataTable->getConfig()->getValueRowAttributes() as $key => $value) {
                if (is_callable($value)) {
                    $value = $value($data, $dataTable);
                }

                $valueRowView->vars['attr'][$key] = $value;
            }

            $valueRowView->vars['attr']['data-index'] = $index;

            foreach ($columns as $column) {
                $valueRowView->children[$column->getName()] = $column->createValueView($valueRowView);
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
        $formFactory = $dataTable->getConfig()->getExportFormFactory();

        $formBuilder = $formFactory->createNamedBuilder(
            name: $dataTable->getConfig()->getExportParameterName(),
            type: ExportDataType::class,
            options: [
                'method' => 'POST',
                'exporters' => $dataTable->getConfig()->getExporters(),
                'default_filename' => $dataTable->getConfig()->getName(),
            ],
        );

        $form = $formBuilder->getForm();
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
}

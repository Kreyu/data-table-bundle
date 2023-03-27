<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Action\ActionInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Form\Type\ExportDataType;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\Form\Type\FiltrationDataType;
use Kreyu\Bundle\DataTableBundle\HeaderRowView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;
use Kreyu\Bundle\DataTableBundle\Personalization\Form\Type\PersonalizationDataType;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumnData;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\ValueRowView;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DataTableType implements DataTableTypeInterface
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $setters = [
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

    private function createHeaderRowView(DataTableView $view, DataTableInterface $dataTable): HeaderRowView
    {
        $headerRowView = new HeaderRowView($view);
        $headerRowView->vars['row'] = $headerRowView;

        foreach ($dataTable->getConfig()->getColumns() as $column) {
            $headerRowView->children[$column->getName()] = $column->createHeaderView($headerRowView);
        }

        return $headerRowView;
    }

    public function createValueRowsViews(DataTableView $view, DataTableInterface $dataTable): array
    {
        $valueRowsViews = [];

        foreach ($dataTable->getPagination()->getItems() as $index => $data) {
            $valueRowView = new ValueRowView($view, $index, $data);
            $valueRowView->vars['row'] = $valueRowView;

            foreach ($dataTable->getConfig()->getColumns() as $column) {
                $valueRowView->children[$column->getName()] = $column->createValueView($valueRowView);
            }

            $valueRowsViews[] = $valueRowView;
        }

        return $valueRowsViews;
    }

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
        $visibleColumns = $this->getVisibleColumns($dataTable);

        $view->vars = array_replace($view->vars, [
            'name' => $dataTable->getConfig()->getName(),
            'exporters' => $dataTable->getConfig()->getExporters(),
            'column_count' => count($visibleColumns),
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
            'translation_domain' => $options['translation_domain'],
        ]);

        $view->vars['header_row'] = $headerRow = $this->createHeaderRowView($view, $dataTable);
        $view->vars['value_rows'] = $this->createValueRowsViews($view, $dataTable);

        $view->vars['actions'] = array_map(
            fn (ActionInterface $action) => $action->createView($view),
            $dataTable->getConfig()->getActions(),
        );

        $view->vars['filters'] = $filters = array_map(
            fn (FilterInterface $filter) => $filter->createView($view),
            $dataTable->getConfig()->getFilters(),
        );

        $view->vars['filtration_form'] = $this->createFiltrationFormView($filters, $dataTable);
        $view->vars['export_form'] = $this->createExportFormView($dataTable);
        $view->vars['personalization_form'] = $this->createPersonalizationFormView($headerRow->children, $dataTable);

        $view->vars['pagination'] = new PaginationView($view, $dataTable->getPagination());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
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
        ]);
    }

    public function getName(): string
    {
        return 'data_table';
    }

    public function getParent(): ?string
    {
        return null;
    }

    /**
     * @return array<ColumnInterface>
     */
    private function getVisibleColumns(DataTableInterface $dataTable): array
    {
        $columns = $dataTable->getConfig()->getColumns();
        $personalizationData = $dataTable->getPersonalizationData();

        if ($dataTable->getConfig()->isPersonalizationEnabled() && null !== $personalizationData) {
            return $personalizationData->compute($columns);
        }

        return $columns;
    }

    /**
     * @param array<FilterView> $filters
     */
    private function createFiltrationFormView(array $filters, DataTableInterface $dataTable): FormView
    {
        $formFactory = $dataTable->getConfig()->getFiltrationFormFactory();

        $formBuilder = $formFactory->createNamedBuilder(
            name: $dataTable->getConfig()->getFiltrationParameterName(),
            type: FiltrationDataType::class,
            options: [
                'filters' => $filters,
            ],
        );

        $form = $formBuilder->getForm();
        $form->setData($dataTable->getFiltrationData());

        return $form->createView();
    }

    /**
     * @param array<ColumnHeaderView> $columns
     */
    private function createPersonalizationFormView(array $columns, DataTableInterface $dataTable): FormView
    {
        $formFactory = $dataTable->getConfig()->getPersonalizationFormFactory();

        $formBuilder = $formFactory->createNamedBuilder(
            name: $dataTable->getConfig()->getPersonalizationParameterName(),
            type: PersonalizationDataType::class,
            options: [
                'columns' => $columns,
            ],
        );

        $form = $formBuilder->getForm();
        $form->setData($dataTable->getPersonalizationData());

        return $form->createView();
    }

    private function createExportFormView(DataTableInterface $dataTable): FormView
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

        return $form->createView();
    }
}

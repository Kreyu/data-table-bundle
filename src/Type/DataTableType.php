<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\DataTableBuilderInterface;
use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\HeadersRowView;
use Kreyu\Bundle\DataTableBundle\Pagination\PaginationView;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Kreyu\Bundle\DataTableBundle\ValuesRowView;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class DataTableType implements DataTableTypeInterface
{
    public function buildDataTable(DataTableBuilderInterface $builder, array $options): void
    {
        $builder
            ->setPersonalizationEnabled($options['personalization_enabled'])
            ->setPersonalizationPersistenceEnabled($options['personalization_persistence_enabled'])
            ->setPersonalizationPersistenceAdapter($options['personalization_persistence_adapter'])
            ->setPersonalizationPersistenceSubject($options['personalization_persistence_subject'])
            ->setPersonalizationFormFactory($options['personalization_form_factory'])
            ->setFiltrationEnabled($options['filtration_enabled'])
            ->setFiltrationPersistenceEnabled($options['filtration_persistence_enabled'])
            ->setFiltrationPersistenceAdapter($options['filtration_persistence_adapter'])
            ->setFiltrationPersistenceSubject($options['filtration_persistence_subject'])
            ->setFiltrationFormFactory($options['filtration_form_factory'])
            ->setSortingEnabled($options['sorting_enabled'])
            ->setSortingPersistenceEnabled($options['sorting_persistence_enabled'])
            ->setSortingPersistenceAdapter($options['sorting_persistence_adapter'])
            ->setSortingPersistenceSubject($options['sorting_persistence_subject'])
            ->setPaginationEnabled($options['pagination_enabled'])
            ->setPaginationPersistenceEnabled($options['pagination_persistence_enabled'])
            ->setPaginationPersistenceAdapter($options['pagination_persistence_adapter'])
            ->setPaginationPersistenceSubject($options['pagination_persistence_subject'])
            ->setExportingEnabled($options['exporting_enabled'])
            ->setRequestHandler($options['request_handler'])
        ;
    }

    public function buildView(DataTableView $view, DataTableInterface $dataTable, array $options): void
    {
        $columns = $dataTable->getConfig()->getColumns();

        if ($dataTable->getConfig()->isPersonalizationEnabled()) {
            /** @var PersonalizationData $personalizationData */
            $personalizationData = $dataTable->getPersonalizationForm()->getData();

            $columns = $personalizationData->compute($columns);
        }

        $personalizationForm = $dataTable->getPersonalizationForm()->createView();
        $personalizationForm->vars['data_table'] = $view;

        $view->vars += [
            'columns' => $columns,
            'filters' => $dataTable->getConfig()->getFilters(),
            'exporters' => $dataTable->getConfig()->getExporters(),
            'personalization_enabled' => $dataTable->getConfig()->isPersonalizationEnabled(),
            'filtration_enabled' => $dataTable->getConfig()->isFiltrationEnabled(),
            'sorting_enabled' => $dataTable->getConfig()->isSortingEnabled(),
            'pagination_enabled' => $dataTable->getConfig()->isPaginationEnabled(),
            'exporting_enabled' => $dataTable->getConfig()->isExportingEnabled(),
            'page_parameter_name' => $dataTable->getConfig()->getPageParameterName(),
            'per_page_parameter_name' => $dataTable->getConfig()->getPerPageParameterName(),
            'sort_parameter_name' => $dataTable->getConfig()->getSortParameterName(),
            'filtration_parameter_name' => $dataTable->getConfig()->getFiltrationParameterName(),
            'personalization_parameter_name' => $dataTable->getConfig()->getPersonalizationParameterName(),
            'export_parameter_name' => $dataTable->getConfig()->getExportParameterName(),
            'filtration_form' => $dataTable->getFiltrationForm()->createView(),
            'personalization_form' => $personalizationForm,
            'export_form' => $dataTable->getExportForm()->createView(),
            'values_rows' => [],
        ];

        $view->vars['has_active_filters'] = !empty(array_filter($view->vars['filtration_form']->children, function (FormView $child) {
            $data = $child->vars['data'];

            return $data instanceof FilterData && $data->hasValue();
        }));

        foreach ($dataTable->getPagination()->getItems() as $item) {
            $view->vars['values_rows'][] = new ValuesRowView($view, $item);
        }

        $view->vars['pagination'] = new PaginationView($view, $dataTable->getPagination());
        $view->vars['headers_row'] = new HeadersRowView($view);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'personalization_enabled' => false,
            'personalization_persistence_enabled' => false,
            'personalization_persistence_adapter' => null,
            'personalization_persistence_subject' => null,
            'personalization_form_factory' => null,
            'filtration_enabled' => false,
            'filtration_persistence_enabled' => false,
            'filtration_persistence_adapter' => null,
            'filtration_persistence_subject' => null,
            'filtration_form_factory' => null,
            'sorting_enabled' => false,
            'sorting_persistence_enabled' => false,
            'sorting_persistence_adapter' => null,
            'sorting_persistence_subject' => null,
            'pagination_enabled' => false,
            'pagination_persistence_enabled' => false,
            'pagination_persistence_adapter' => null,
            'pagination_persistence_subject' => null,
            'exporting_enabled' => false,
            'request_handler' => null,
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
}
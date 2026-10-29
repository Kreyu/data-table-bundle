<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrationDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var DataTableInterface $dataTable
         */
        $dataTable = $options['data_table'];

        foreach ($dataTable->getFilters() as $filter) {
            $builder->add(
                $filter->getFormName(),
                FilterDataType::class,
                $filter->getFormOptions()
                + [
                    'getter' => static fn (FiltrationData $filtrationData) => $filtrationData->getFilterData($filter),
                    'setter' => static fn (
                        FiltrationData $filtrationData,
                        FilterData $filterData,
                    ) => $filtrationData->setFilterData($filter, $filterData),
                ],
            );
        }
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /**
         * @var DataTableInterface $dataTable
         */
        $dataTable = $options['data_table'];

        $dataTableView = $options['data_table_view'];

        if (!$dataTableView instanceof DataTableView) {
            throw new \LogicException('Unable to create filtration form view without the data table view.');
        }

        $this->applyFormAttributeRecursively($view, $id = $view->vars['id']);

        $view->vars['attr']['id'] = $id;

        $searchFields = [];

        foreach ($dataTable->getFilters() as $filterName => $filter) {
            $formName = $filter->getFormName();
            $filterFormView = $view->children[$formName] ?? null;

            if ($filterFormView === null) {
                continue;
            }

            $filterView = $dataTableView->filters[$filterName];

            $filterFormView->vars['label'] = $filterView->vars['label'];
            $filterFormView->vars['translation_domain'] = $filterView->vars['translation_domain'];

            $filterInnerType = $filter->getConfig()->getType()->getInnerType();

            if ($filterInnerType instanceof SearchFilterTypeInterface) {
                $searchFields[$formName] = $filterFormView;

                unset($view[$formName]);
            }
        }

        $view->vars['search_fields'] = $searchFields;

        $headerFields = $this->moveHeaderFields($view, $dataTable, $dataTableView);

        $view->vars['header_fields'] = $headerFields;
        $dataTableView->vars['has_header_filters'] = $headerFields !== [];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'method' => 'GET',
                'data_class' => FiltrationData::class,
                'csrf_protection' => false,
                'data_table_view' => null,
            ])
            ->setRequired('data_table')
            ->setAllowedTypes('data_table', DataTableInterface::class)
            ->setAllowedTypes('data_table_view', ['null', DataTableView::class])
        ;
    }

    /**
     * @return array<string, FormView>
     */
    private function moveHeaderFields(
        FormView $view,
        DataTableInterface $dataTable,
        DataTableView $dataTableView,
    ): array {
        $headerFields = [];
        $columnHeaderViews = $dataTableView->headerRow->children ?? [];

        foreach ($dataTable->getVisibleColumns() as $name => $column) {
            $hasHeaderFilter = $column->getConfig()->getOption('filter') !== null;

            if (!$hasHeaderFilter) {
                continue;
            }

            $hasFilter = $dataTable->hasFilter($name);

            if (!$hasFilter) {
                continue;
            }

            $formName = $dataTable->getFilter($name)->getFormName();
            $filterFormView = $view->children[$formName] ?? null;

            if ($filterFormView === null) {
                continue;
            }

            $headerFields[$formName] = $filterFormView;
            $columnHeaderViews[$name]->vars['filter_form'] = $filterFormView;

            unset($view[$formName]);
        }

        return $headerFields;
    }

    private function applyFormAttributeRecursively(FormView $view, string $id): void
    {
        $view->vars['attr']['form'] = $id;

        foreach ($view->children as $child) {
            $this->applyFormAttributeRecursively($child, $id);
        }
    }
}

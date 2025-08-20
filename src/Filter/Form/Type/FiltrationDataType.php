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

        $filters = $dataTable->getFilters();

        if (null !== $options['filters']) {
            $selected = [];
            foreach ($options['filters'] as $filter) {
                $selected[$filter->getName()] = $filter;
            }
            $filters = $selected;
        }

        foreach ($filters as $filter) {
            $builder->add($filter->getFormName(), FilterDataType::class, $filter->getFormOptions() + [
                'getter' => fn (FiltrationData $filtrationData) => $filtrationData->getFilterData($filter),
                'setter' => fn (FiltrationData $filtrationData, FilterData $filterData) => $filtrationData->setFilterData($filter, $filterData),
            ]);
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

        foreach ($view as $name => $filterFormView) {
            if (isset($dataTableView->filters[$name])) {
                $filterView = $dataTableView->filters[$name];
                $filterFormView->vars['label'] = $filterView->vars['label'];
                $filterFormView->vars['translation_domain'] = $filterView->vars['translation_domain'];
            }
        }

        $searchFields = [];

        foreach ($form as $child) {
            if (!$dataTable->hasFilter($child->getName())) {
                // This may be a column filter not registered in DataTable->getFilters(); skip.
                continue;
            }

            $filter = $dataTable->getFilter($child->getName());

            if ($filter->getConfig()->getType()->getInnerType() instanceof SearchFilterTypeInterface) {
                $searchFields[] = $view[$child->getName()];

                unset($view[$child->getName()]);
            }
        }

        $view->vars['search_fields'] = $searchFields;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'method' => 'GET',
                'data_class' => FiltrationData::class,
                'csrf_protection' => false,
                'data_table_view' => null,
                'filters' => null,
            ])
            ->setRequired('data_table')
            ->setAllowedTypes('data_table', DataTableInterface::class)
            ->setAllowedTypes('data_table_view', ['null', DataTableView::class])
            ->setAllowedTypes('filters', ['null', 'array'])
        ;
    }

    private function applyFormAttributeRecursively(FormView $view, string $id): void
    {
        $view->vars['attr']['form'] = $id;

        foreach ($view->children as $child) {
            $this->applyFormAttributeRecursively($child, $id);
        }
    }
}

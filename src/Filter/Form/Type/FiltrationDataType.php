<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrationDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var DataTableInterface $dataTable
         */
        $dataTable = $options['data_table'];

        foreach ($dataTable->getConfig()->getFilters() as $filter) {
            $builder->add($filter->getFormName(), FilterDataType::class, array_merge($filter->getFormOptions() + [
                'getter' => function (FiltrationData $filtrationData, FormInterface $form) {
                    return $filtrationData->getFilterData($form->getName());
                },
                'setter' => function (FiltrationData $filtrationData, FilterData $filterData, FormInterface $form) {
                    $filtrationData->setFilterData($form->getName(), $filterData);
                },
                'empty_data' => new FilterData,
            ]));
        }
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /**
         * @var DataTableInterface $dataTable
         */
        $dataTable = $options['data_table'];

        /**
         * @var DataTableView $dataTableView
         */
        $dataTableView = $options['data_table_view'];

        if (null === $dataTableView) {
            throw new \LogicException('Unable to create filtration form view without the data table view.');
        }

        $view->vars['attr']['id'] = $view->vars['id'];

        foreach ($view as $name => $filterFormView) {
            $filterView = $dataTableView->filters[$name];

            $filterFormView->vars = array_replace($filterFormView->vars, [
                'label' => $filterView->vars['label'],
                'translation_domain' => $filterView->vars['translation_domain'],
            ]);
        }

        $searchFields = [];

        foreach ($form as $child) {
            try {
                $filter = $dataTable->getConfig()->getFilter($child->getName());
            } catch (\InvalidArgumentException) {
                continue;
            }

            if ($filter->getType()->getInnerType() instanceof SearchFilterTypeInterface) {
                $searchField = $view[$child->getName()];
                $searchField->vars['attr']['form'] = $view->vars['id'];

                $searchFields[] = $searchField;

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
                'filters' => [],
            ])
            ->setRequired('data_table')
            ->setAllowedTypes('data_table', DataTableInterface::class)
            ->setAllowedTypes('data_table_view', ['null', DataTableView::class])
            ->setAllowedTypes('filters', FilterInterface::class . '[]')
        ;
    }
}

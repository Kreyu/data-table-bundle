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
            $builder->add($filter->getFormName(), FilterDataType::class, $filter->getFormOptions() + [
                'getter' => fn (FiltrationData $filtrationData) => $filtrationData->getFilterData($filter),
                'setter' => fn (FiltrationData $filtrationData, FilterData $filterData) => $filtrationData->setFilterData($filter, $filterData),
            ]);

            $builder->setAttributes(['a' => 'b']);
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
            $filterView = $dataTableView->filters[$name];

            $filterFormView->vars['label'] = $filterView->vars['label'];
            $filterFormView->vars['translation_domain'] = $filterView->vars['translation_domain'];
        }

        foreach ($view as $name => $filterFormView) {
            try {
                $filter = $dataTable->getFilter($name);
            } catch (\InvalidArgumentException) {
                continue;
            }

            $filterFormView->vars['search_filter'] = $filter->getConfig()->getType()->getInnerType() instanceof SearchFilterTypeInterface;
        }
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

    private function applyFormAttributeRecursively(FormView $view, string $id): void
    {
        $view->vars['attr']['form'] = $id;

        foreach ($view->children as $child) {
            $this->applyFormAttributeRecursively($child, $id);
        }
    }
}

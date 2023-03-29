<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\DataTableView;
use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
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
         * @var FilterInterface $filter
         */
        foreach ($options['filters'] as $filter) {
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
        /** @var DataTableView $dataTable */
        if (null === $dataTable = $options['data_table']) {
            throw new \LogicException('Unable to create filtration form view without the data table view.');
        }

        foreach ($view as $name => $filterFormView) {
            $filterView = $dataTable->filters[$name];

            $filterFormView->vars = array_replace($filterFormView->vars, [
                'label' => $filterView->vars['label'],
                'translation_domain' => $filterView->vars['translation_domain'],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'method' => 'GET',
                'data_class' => FiltrationData::class,
                'csrf_protection' => false,
                'data_table' => null,
                'filters' => [],
            ])
            ->setAllowedTypes('data_table', ['null', DataTableView::class])
            ->setAllowedTypes('filters', FilterInterface::class . '[]')
        ;
    }
}

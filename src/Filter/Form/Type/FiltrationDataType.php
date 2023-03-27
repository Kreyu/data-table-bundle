<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterView;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrationDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var FilterView $filter
         */
        foreach ($options['filters'] as $filter) {
            $builder->add($filter->getFormName(), FilterDataType::class, array_merge($filter->getFormOptions() + [
                'getter' => function (FiltrationData $filtrationData, FormInterface $form) {
                    return $filtrationData->getFilterData($form->getName());
                },
                'setter' => function (FiltrationData $filtrationData, FilterData $filterData, FormInterface $form) {
                    $filtrationData->setFilterData($form->getName(), $filterData);
                },
            ]));
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'method' => 'GET',
                'data_class' => FiltrationData::class,
                'csrf_protection' => false,
            ])
            ->setRequired('filters')
            ->setAllowedTypes('filters', FilterView::class.'[]')
        ;
    }
}

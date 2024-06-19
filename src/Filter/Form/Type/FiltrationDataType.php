<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Kreyu\Bundle\DataTableBundle\Filter\Type\SearchFilterTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrationDataType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var DataTableInterface $dataTable
         */
        $dataTable = $options['data_table'];

        foreach ($dataTable->getFilters() as $filter) {
            $builder->add($filter->getFormName(), FilterDataType::class, $filter->getFormOptions());
        }

        $builder->setDataMapper($this);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        /**
         * @var DataTableInterface $dataTable
         */
        $dataTable = $options['data_table'];

        $this->applyFormAttributeRecursively($view, $id = $view->vars['id']);

        $view->vars['attr']['id'] = $id;

        $searchFields = [];

        foreach ($form as $child) {
            try {
                $filter = $dataTable->getFilter($child->getName());
            } catch (\InvalidArgumentException) {
                continue;
            }

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
                'allow_extra_fields' => true,
            ])
            ->setRequired('filters')
            ->setAllowedTypes('filters', FilterInterface::class.'[]')
        ;
    }

    private function applyFormAttributeRecursively(FormView $view, string $id): void
    {
        $view->vars['attr']['form'] = $id;

        foreach ($view->children as $child) {
            $this->applyFormAttributeRecursively($child, $id);
        }
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms): void
    {
        if (!$viewData instanceof FiltrationData) {
            return;
        }

        $forms = iterator_to_array($forms);

        /** @var FormInterface[] $forms */

        foreach ($viewData->getFilters() as $name => $filterData) {
            $forms[$name]->setData($filterData);
        }
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData): void
    {
        $forms = iterator_to_array($forms);

        /** @var FormInterface[] $forms */

        $filters = [];

        foreach ($forms as $name => $form) {
            $filters[$name] = $form->getData();
        }

        $viewData = new FiltrationData(filters: $filters);
    }
}

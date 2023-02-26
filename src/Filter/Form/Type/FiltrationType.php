<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrationType extends AbstractType implements DataMapperInterface
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FilterInterface $filter */
        foreach ($options['filters'] as $filter) {
            $builder->add($filter->getFormName(), FilterDataType::class, $filter->getFormOptions());
        }

        $builder->setDataMapper($this);
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
            ->setAllowedTypes('filters', FilterInterface::class.'[]')
        ;
    }

    public function mapDataToForms(mixed $viewData, \Traversable $forms)
    {
    }

    public function mapFormsToData(\Traversable $forms, mixed &$viewData)
    {
        $forms = iterator_to_array($forms);

        $data = [];

        foreach ($forms as $child) {
            $data[$child->getName()] = $child->getData();
        }

        $viewData = new FiltrationData($data);
    }
}

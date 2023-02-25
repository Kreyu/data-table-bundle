<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FilterInterface $filter */
        foreach ($options['filters'] as $filter) {
            $builder->add($filter->getFormName(), FilterDataType::class, $filter->getFormOptions());
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('csrf_protection', false)
            ->setRequired('filters')
            ->setAllowedTypes('filters', FilterInterface::class.'[]')
        ;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('operator', $options['operator_type'], $options['operator_options'] + [
                'label' => false,
                'required' => false,
            ])
            ->add('value', $options['field_type'], $options['field_options'] + [
                'label' => false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'required' => false,
            'operator_type' => OperatorType::class,
            'operator_options' => [
                'visible' => false,
            ],
            'field_type' => TextType::class,
            'field_options' => [],
        ]);

        $resolver->setAllowedTypes('operator_type', 'string');
        $resolver->setAllowedTypes('operator_options', 'array');

        $resolver->setAllowedTypes('field_type', 'string');
        $resolver->setAllowedTypes('field_options', 'array');
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_filter';
    }
}

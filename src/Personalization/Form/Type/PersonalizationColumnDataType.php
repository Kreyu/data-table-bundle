<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Form\Type;

use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumnData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationColumnDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', HiddenType::class)
            ->add('priority', HiddenType::class)
            ->add('visible', HiddenType::class)
        ;

        // Add transformer that converts boolean to integer to help the HiddenType visibility field.
        $builder->get('visible')->addModelTransformer(new CallbackTransformer(
            fn (?bool $visible) => (int) $visible,
            fn (int $visible) => (bool) $visible,
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalizationColumnData::class,
            'empty_data' => fn (FormInterface $form) => new PersonalizationColumnData($form->getName()),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_personalization_column_data';
    }
}

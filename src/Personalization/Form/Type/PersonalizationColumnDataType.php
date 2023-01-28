<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Form\Type;

use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumnData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationColumnDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('order', HiddenType::class)
            ->add('visible', HiddenType::class, [
                'getter' => fn (PersonalizationColumnData $column) => (int) $column->isVisible(),
                'setter' => fn (PersonalizationColumnData $column, mixed $value) => $column->setVisible((bool) $value),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalizationColumnData::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_personalization_column';
    }
}

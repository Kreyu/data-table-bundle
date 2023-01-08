<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Form\Type;

use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumn;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationColumnType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('order', HiddenType::class)
            ->add('visible', HiddenType::class, [
                'getter' => fn (PersonalizationColumn $column) => (int) $column->isVisible(),
                'setter' => fn (PersonalizationColumn $column, mixed $value) => $column->setVisible((bool) $value),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalizationColumn::class,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_personalization_column';
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Form\Type;

use Kreyu\Bundle\DataTableBundle\DataTableInterface;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationColumnData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationColumnDataType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        /**
         * @var DataTableInterface $dataTable
         */
        $dataTable = $options['data_table'];

        $column = $dataTable->getConfig()->getColumn($form->getName());

        $view->vars['column'] = $column->createView();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', HiddenType::class)
            ->add('order', HiddenType::class)
            ->add('visible', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalizationColumnData::class,
            'translation_domain' => 'KreyuDataTable',
        ]);

        $resolver->setRequired('data_table');
        $resolver->setAllowedTypes('data_table', DataTableInterface::class);
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_personalization_column_data';
    }
}

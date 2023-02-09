<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Form\Type;

use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationDataType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr']['id'] = $form->getName();

        usort($view['columns']->children, function (FormView $a, FormView $b) {
            return $a->vars['data']->getOrder() <=> $b->vars['data']->getOrder();
        });
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('columns', CollectionType::class, [
            'entry_type' => PersonalizationColumnDataType::class,
            'entry_options' => [
                'data_table' => $options['data_table'],
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PersonalizationData::class,
            'translation_domain' => 'KreyuDataTable',
        ]);

        $resolver->setRequired('data_table');
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_personalization';
    }
}

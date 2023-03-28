<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Personalization\Form\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnHeaderView;
use Kreyu\Bundle\DataTableBundle\Personalization\PersonalizationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalizationDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('columns', CollectionType::class, [
            'entry_type' => PersonalizationColumnDataType::class,
        ]);
    }

    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        foreach ($view['columns'] as $name => $columnView) {
            if (!array_key_exists($name, $options['columns'])) {
                unset($view['columns'][$name]);
                continue;
            }

            $columnHeaderView = $options['columns'][$name];

            $columnView->vars['label'] = $columnHeaderView->vars['label'];
            $columnView->vars['translation_domain'] = $columnHeaderView->vars['translation_domain'];
            $columnView->vars['translation_parameters'] = $columnHeaderView->vars['translation_parameters'];
        }

        usort($view['columns']->children, function (FormView $columnA, FormView $columnB) {
            return $columnA->vars['data']->getOrder() <=> $columnB->vars['data']->getOrder();
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => PersonalizationData::class,
            ])
            ->setRequired('columns')
            ->setAllowedTypes('columns', ColumnHeaderView::class . '[]')
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'kreyu_data_table_personalization_data';
    }
}

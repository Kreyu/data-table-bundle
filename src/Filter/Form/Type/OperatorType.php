<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Filter\Operator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OperatorType extends AbstractType
{
    public function finishView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['visible'] = $options['visible'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'class' => Operator::class,
                'choice_translation_domain' => 'KreyuDataTable',
                'choice_label' => 'label',
                'label' => false,
                'placeholder' => false,
                'visible' => false,
                'required' => false,
            ])
            ->setAllowedTypes('visible', 'bool')
        ;
    }

    public function getParent(): string
    {
        return EnumType::class;
    }
}

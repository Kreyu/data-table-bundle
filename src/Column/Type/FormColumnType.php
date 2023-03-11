<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormColumnType extends AbstractColumnType
{
    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
        if (null === $view->vars['form_child_path']) {
            $view->vars['form_child_path'] = $column->getName();
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('form')
            ->setDefault('form_child_path', null)
            ->setAllowedTypes('form', FormInterface::class)
            ->setAllowedTypes('form_child_path', ['null', 'bool', 'string'])
        ;
    }
}

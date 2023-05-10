<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnValueView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormColumnType extends AbstractColumnType
{
    public function buildValueView(ColumnValueView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'form' => $options['form'],
            'form_child_path' => $options['form_child_path'] ?? $column->getName(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('form')
            ->setDefault('form_child_path', null)
            ->setAllowedTypes('form', FormInterface::class)
            ->setAllowedTypes('form_child_path', ['null', 'bool', 'string'])
            ->setInfo('form', 'An instance of the form which wraps the data table.')
            ->setInfo('form_child_path', 'A path to the child form of each collection field.')
        ;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Kreyu\Bundle\DataTableBundle\Column\ColumnInterface;
use Kreyu\Bundle\DataTableBundle\Column\ColumnView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateColumnType extends AbstractColumnType
{
    public function buildView(ColumnView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'template_path' => $options['template_path'],
            'template_vars' => $options['template_vars'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'template_path',
            ])
            ->setDefaults([
                'template_vars' => [],
            ])
            ->setAllowedTypes('template_path', ['string'])
            ->setAllowedTypes('template_vars', ['array'])
            ->setInfo('template_path', 'A path to the template that should be rendered.')
            ->setInfo('template_vars', 'An array of variables passed to the template.')
        ;
    }
}

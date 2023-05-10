<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateColumnType extends AbstractColumnType
{
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

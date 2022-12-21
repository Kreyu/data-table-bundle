<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'template_path' => null,
                'template_vars' => [],
            ])
            ->setAllowedTypes('template_path', ['null', 'string'])
            ->setAllowedTypes('template_vars', ['array'])
        ;
    }
}

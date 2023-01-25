<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TemplateType extends AbstractType
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
        ;
    }
}

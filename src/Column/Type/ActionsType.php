<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionsType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('property_path', false)
            ->setDefault('actions', function (OptionsResolver $resolver) {
                $resolver
                    ->setPrototype(true)
                    ->setRequired([
                        'template_path',
                    ])
                    ->setDefaults([
                        'template_vars' => [],
                    ])
                    ->setAllowedTypes('template_path', ['string', 'callable'])
                    ->setAllowedTypes('template_vars', ['array', 'callable'])
                ;
            })
        ;
    }
}

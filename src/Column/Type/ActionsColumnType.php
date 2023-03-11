<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ActionsColumnType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'export' => false,
                'property_path' => false,
                'display_personalization_button' => true,
                'actions' => function (OptionsResolver $resolver) {
                    $resolver
                        ->setPrototype(true)
                        ->setRequired([
                            'template_path',
                        ])
                        ->setDefaults([
                            'template_vars' => [],
                        ])
                        ->setAllowedTypes('template_path', ['string', \Closure::class])
                        ->setAllowedTypes('template_vars', ['array', \Closure::class])
                    ;
                },
            ])
        ;
    }
}

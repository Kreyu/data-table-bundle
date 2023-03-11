<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkColumnType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'href' => '#',
                'target' => '_self',
                'display_icon' => true,
            ])
            ->setAllowedTypes('href', ['string', \Closure::class])
            ->setAllowedTypes('target', ['string', \Closure::class])
            ->setAllowedTypes('display_icon', ['boolean'])
        ;
    }
}

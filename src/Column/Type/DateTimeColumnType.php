<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DateTimeColumnType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'format' => 'd.m.Y H:i:s',
                'timezone' => null,
            ])
            ->setAllowedTypes('format', ['string'])
            ->setAllowedTypes('timezone', ['null', 'string'])
        ;
    }
}

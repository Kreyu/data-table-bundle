<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class FormActionType extends AbstractActionType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'method' => 'GET',
                'action' => '#',
                'button_attr' => [],
                'icon_attr' => [],
            ])
            ->setAllowedTypes('method', ['string', 'callable'])
            ->setAllowedTypes('action', ['string', 'callable'])
            ->setAllowedTypes('button_attr', ['array', 'callable'])
            ->setAllowedTypes('icon_attr', ['array', 'callable'])
        ;
    }
}

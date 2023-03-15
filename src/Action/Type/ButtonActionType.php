<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Action\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonActionType extends AbstractActionType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'display_icon' => false,
        ]);
    }

    public function getParent(): ?string
    {
        return LinkActionType::class;
    }
}

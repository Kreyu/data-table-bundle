<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Translation\TranslatableMessage;

class BooleanType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'label_true' => 'Yes',
                'label_false' => 'No',
            ])
            ->setAllowedTypes('label_true', ['string', TranslatableMessage::class])
            ->setAllowedTypes('label_false', ['string', TranslatableMessage::class]);
    }
}

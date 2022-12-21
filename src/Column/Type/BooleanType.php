<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use function Symfony\Component\Translation\t;

class BooleanType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults([
                'label_true' => t('Yes', domain: 'KreyuDataTable'),
                'label_false' => t('No', domain: 'KreyuDataTable'),
            ])
            ->setAllowedTypes('label_true', ['string', TranslatableMessage::class])
            ->setAllowedTypes('label_false', ['string', TranslatableMessage::class]);
    }
}

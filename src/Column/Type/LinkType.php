<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('url', '#')
            ->setAllowedTypes('url', ['string', 'callable']);
    }
}

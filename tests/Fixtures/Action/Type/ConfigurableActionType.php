<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Action\Type;

use Kreyu\Bundle\DataTableBundle\Action\Type\AbstractActionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfigurableActionType extends AbstractActionType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'foo' => null,
            'bar' => null,
        ]);
    }
}

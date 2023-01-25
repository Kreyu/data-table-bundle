<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Form\Type;

use Kreyu\Bundle\DataTableBundle\Filter\FiltrationData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltrationDataType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', FiltrationData::class);
    }
}
<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

final class DateColumnType extends AbstractColumnType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('format', 'd.m.Y');
    }

    public function getParent(): ?string
    {
        return DateTimeColumnType::class;
    }
}

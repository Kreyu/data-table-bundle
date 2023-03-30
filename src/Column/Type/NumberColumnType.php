<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Column\Type;

class NumberColumnType extends AbstractColumnType
{
    public function getParent(): ?string
    {
        return TextColumnType::class;
    }
}

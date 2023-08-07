<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Extension\I18n;

use Kreyu\Bundle\DataTableBundle\Column\Type\NumberColumnType;

class IntlNumberColumnTypeExtension extends AbstractIntlColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [
            NumberColumnType::class,
        ];
    }
}

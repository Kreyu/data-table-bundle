<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Extension;

use Kreyu\Bundle\DataTableBundle\Column\Extension\AbstractColumnTypeExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Column\Type\SimpleColumnType;

class SimpleColumnTypeFooExtension extends AbstractColumnTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield SimpleColumnType::class;
    }
}

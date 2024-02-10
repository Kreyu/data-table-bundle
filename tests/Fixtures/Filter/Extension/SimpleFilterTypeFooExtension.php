<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Extension;

use Kreyu\Bundle\DataTableBundle\Filter\Extension\AbstractFilterTypeExtension;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\Filter\Type\SimpleFilterType;

class SimpleFilterTypeFooExtension extends AbstractFilterTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield SimpleFilterType::class;
    }
}

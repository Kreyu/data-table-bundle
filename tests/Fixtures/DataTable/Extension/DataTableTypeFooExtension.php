<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Extension;

use Kreyu\Bundle\DataTableBundle\Extension\AbstractDataTableTypeExtension;
use Kreyu\Bundle\DataTableBundle\Type\DataTableType;

class DataTableTypeFooExtension extends AbstractDataTableTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        yield DataTableType::class;
    }
}

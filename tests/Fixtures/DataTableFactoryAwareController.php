<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures;

use Kreyu\Bundle\DataTableBundle\DataTableFactoryAwareTrait;

final class DataTableFactoryAwareController
{
    use DataTableFactoryAwareTrait {
        createDataTable as public;
        createNamedDataTable as public;
        createDataTableBuilder as public;
        createNamedDataTableBuilder as public;
    }
}

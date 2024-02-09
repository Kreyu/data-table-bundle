<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class RecursiveDataTableTypeBar extends AbstractDataTableType
{
    public function getParent(): string
    {
        return RecursiveDataTableTypeBaz::class;
    }
}

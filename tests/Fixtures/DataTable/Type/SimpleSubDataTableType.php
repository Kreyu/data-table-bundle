<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class SimpleSubDataTableType extends AbstractDataTableType
{
    public function getParent(): string
    {
        return SimpleDataTableType::class;
    }
}

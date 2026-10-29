<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\CustomQuery;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class DefaultQueryDataTableType extends AbstractDataTableType
{
    public function createDefaultQuery(array $options): mixed
    {
        return new CustomQuery();
    }
}

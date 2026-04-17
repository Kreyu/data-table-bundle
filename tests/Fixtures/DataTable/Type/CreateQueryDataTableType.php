<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Type;

use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\CustomQuery;
use Kreyu\Bundle\DataTableBundle\Type\AbstractDataTableType;

class CreateQueryDataTableType extends AbstractDataTableType
{
    public function createQuery(array $options): mixed
    {
        return new CustomQuery();
    }
}

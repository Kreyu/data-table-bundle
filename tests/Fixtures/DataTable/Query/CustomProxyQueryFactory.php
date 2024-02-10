<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Kreyu\Bundle\DataTableBundle\Tests\Fixtures\CustomQuery;

class CustomProxyQueryFactory implements ProxyQueryFactoryInterface
{
    public function create(mixed $data): ProxyQueryInterface
    {
        return new CustomProxyQuery();
    }

    public function supports(mixed $data): bool
    {
        return $data instanceof CustomQuery;
    }
}

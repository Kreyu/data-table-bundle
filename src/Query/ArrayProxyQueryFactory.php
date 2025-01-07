<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query;

class ArrayProxyQueryFactory implements ProxyQueryFactoryInterface
{
    public function create(mixed $data): ProxyQueryInterface
    {
        return new ArrayProxyQuery($data);
    }

    public function supports(mixed $data): bool
    {
        return is_array($data);
    }
}

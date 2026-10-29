<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query;

use Kreyu\Bundle\DataTableBundle\Exception\UnexpectedTypeException;

class ArrayProxyQueryFactory implements ProxyQueryFactoryInterface
{
    public function create(mixed $data): ProxyQueryInterface
    {
        if (!is_array($data)) {
            throw new UnexpectedTypeException($data, 'array');
        }

        return new ArrayProxyQuery($data);
    }

    public function supports(mixed $data): bool
    {
        return is_array($data);
    }
}

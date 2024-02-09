<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Tests\Fixtures\DataTable\Query;

use Kreyu\Bundle\DataTableBundle\Exception\InvalidArgumentException;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class BarProxyQueryFactory implements ProxyQueryFactoryInterface
{
    public function create(mixed $data): ProxyQueryInterface
    {
        throw new InvalidArgumentException('Not implemented');
    }

    public function supports(mixed $data): bool
    {
        return true;
    }
}
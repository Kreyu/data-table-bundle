<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\Factory;

use Doctrine\ORM\QueryBuilder;
use Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Query\ProxyQuery;
use Kreyu\Bundle\DataTableBundle\Query\Factory\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

class ProxyQueryFactory implements ProxyQueryFactoryInterface
{
    public function supports(mixed $data): bool
    {
        return $data instanceof QueryBuilder;
    }

    public function create(mixed $data): ProxyQueryInterface
    {
        return new ProxyQuery($data);
    }
}
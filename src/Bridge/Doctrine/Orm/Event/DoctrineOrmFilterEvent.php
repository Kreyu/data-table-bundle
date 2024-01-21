<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Bridge\Doctrine\Orm\Event;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Contracts\EventDispatcher\Event;

class DoctrineOrmFilterEvent extends Event
{
    public function __construct(
        private readonly ProxyQueryInterface $query,
        private readonly FilterData $data,
        private readonly FilterInterface $filter,
    ) {
    }

    public function getQuery(): ProxyQueryInterface
    {
        return $this->query;
    }

    public function getData(): FilterData
    {
        return $this->data;
    }

    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }
}

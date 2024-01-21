<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter\Event;

use Kreyu\Bundle\DataTableBundle\Filter\FilterData;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;
use Symfony\Contracts\EventDispatcher\Event;

class FilterEvent extends Event
{
    public function __construct(
        private readonly ProxyQueryInterface $query,
        private FilterData $data,
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

    public function setData(FilterData $data): void
    {
        $this->data = $data;
    }

    public function getFilter(): FilterInterface
    {
        return $this->filter;
    }
}

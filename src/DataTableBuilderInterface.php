<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

interface DataTableBuilderInterface extends DataTableConfigBuilderInterface
{
    public function getQuery(): ?ProxyQueryInterface;

    public function setQuery(?ProxyQueryInterface $query): static;

    public function getDataTable(): DataTableInterface;
}

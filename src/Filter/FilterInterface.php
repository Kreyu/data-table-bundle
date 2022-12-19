<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Filter;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

interface FilterInterface
{
    public function apply(ProxyQueryInterface $query, FilterData $data): void;

    public function getFormName(): string;

    public function getFormOptions(): array;
}

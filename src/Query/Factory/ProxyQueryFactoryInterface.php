<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query\Factory;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryInterface;

interface ProxyQueryFactoryInterface
{
    public function supports(mixed $data): bool;

    public function create(mixed $data): ProxyQueryInterface;
}
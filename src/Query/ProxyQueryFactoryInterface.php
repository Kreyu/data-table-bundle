<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query;

interface ProxyQueryFactoryInterface
{
    public function create(mixed $data): ProxyQueryInterface;

    public function supports(mixed $data): bool;
}

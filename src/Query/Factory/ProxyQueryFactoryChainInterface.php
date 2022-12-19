<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\Query\Factory;

interface ProxyQueryFactoryChainInterface
{
    public function getDataCompatibleFactory(mixed $data): ProxyQueryFactoryInterface;
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;

interface DataTableRegistryInterface
{
    /**
     * @param class-string<DataTableTypeInterface> $name
     */
    public function getType(string $name): ResolvedDataTableTypeInterface;

    /**
     * @param class-string<DataTableTypeInterface> $name
     */
    public function hasType(string $name): bool;

    /**
     * @return array<ProxyQueryFactoryInterface>
     */
    public function getProxyQueryFactories(): array;
}

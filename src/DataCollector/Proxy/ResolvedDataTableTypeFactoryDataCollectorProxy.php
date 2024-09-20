<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\ResolvedDataTableTypeInterface;

class ResolvedDataTableTypeFactoryDataCollectorProxy implements ResolvedDataTableTypeFactoryInterface
{
    public function __construct(
        private ResolvedDataTableTypeFactoryInterface $proxiedFactory,
        private DataTableDataCollectorInterface $dataCollector,
    ) {
    }

    public function createResolvedType(DataTableTypeInterface $type, array $typeExtensions, ?ResolvedDataTableTypeInterface $parent = null): ResolvedDataTableTypeInterface
    {
        return new ResolvedDataTableTypeDataCollectorProxy(
            $this->proxiedFactory->createResolvedType($type, $typeExtensions, $parent),
            $this->dataCollector,
        );
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\ResolvedFilterTypeInterface;

class ResolvedFilterTypeFactoryDataCollectorProxy implements ResolvedFilterTypeFactoryInterface
{
    public function __construct(
        private ResolvedFilterTypeFactoryInterface $proxiedFactory,
        private DataTableDataCollectorInterface $dataCollector,
    ) {
    }

    public function createResolvedType(FilterTypeInterface $type, array $typeExtensions = [], ?ResolvedFilterTypeInterface $parent = null): ResolvedFilterTypeInterface
    {
        return new ResolvedFilterTypeDataCollectorProxy(
            $this->proxiedFactory->createResolvedType($type, $typeExtensions, $parent),
            $this->dataCollector,
        );
    }
}

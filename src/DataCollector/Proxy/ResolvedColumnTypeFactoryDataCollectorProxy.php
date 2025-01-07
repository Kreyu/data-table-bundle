<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ResolvedColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;

class ResolvedColumnTypeFactoryDataCollectorProxy implements ResolvedColumnTypeFactoryInterface
{
    public function __construct(
        private ResolvedColumnTypeFactoryInterface $proxiedFactory,
        private DataTableDataCollectorInterface $dataCollector,
    ) {
    }

    public function createResolvedType(ColumnTypeInterface $type, array $typeExtensions = [], ?ResolvedColumnTypeInterface $parent = null): ResolvedColumnTypeInterface
    {
        return new ResolvedColumnTypeDataCollectorProxy(
            $this->proxiedFactory->createResolvedType($type, $typeExtensions, $parent),
            $this->dataCollector,
        );
    }
}

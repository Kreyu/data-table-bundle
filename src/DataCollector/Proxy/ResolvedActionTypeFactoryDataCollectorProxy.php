<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ResolvedActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\DataCollector\DataTableDataCollectorInterface;

class ResolvedActionTypeFactoryDataCollectorProxy implements ResolvedActionTypeFactoryInterface
{
    public function __construct(
        private ResolvedActionTypeFactoryInterface $proxiedFactory,
        private DataTableDataCollectorInterface $dataCollector,
    ) {
    }

    public function createResolvedType(ActionTypeInterface $type, array $typeExtensions = [], ?ResolvedActionTypeInterface $parent = null): ResolvedActionTypeInterface
    {
        return new ResolvedActionTypeDataCollectorProxy(
            $this->proxiedFactory->createResolvedType($type, $typeExtensions, $parent),
            $this->dataCollector,
        );
    }
}

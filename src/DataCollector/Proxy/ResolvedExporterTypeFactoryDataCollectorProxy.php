<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DataCollector\Proxy;

use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ResolvedExporterTypeInterface;

class ResolvedExporterTypeFactoryDataCollectorProxy implements ResolvedExporterTypeFactoryInterface
{
    public function __construct(
        private ResolvedExporterTypeFactoryInterface $proxiedFactory,
    ) {
    }

    public function createResolvedType(ExporterTypeInterface $type, array $typeExtensions = [], ?ResolvedExporterTypeInterface $parent = null): ResolvedExporterTypeInterface
    {
        return new ResolvedExporterTypeDataCollectorProxy(
            $this->proxiedFactory->createResolvedType($type, $typeExtensions, $parent),
        );
    }
}

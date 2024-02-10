<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryBuilder;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;

final class DataTables
{
    public static function createExporterFactory(): ExporterFactoryInterface
    {
        return self::createExporterFactoryBuilder()->getExporterFactory();
    }

    public static function createExporterFactoryBuilder(): ExporterFactoryBuilderInterface
    {
        return new ExporterFactoryBuilder();
    }

    private function __construct()
    {
    }
}

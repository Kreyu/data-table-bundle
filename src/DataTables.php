<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryBuilder;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryBuilder;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;

final class DataTables
{
    public static function createActionFactory(): ActionFactoryInterface
    {
        return self::createActionFactoryBuilder()->getActionFactory();
    }

    public static function createExporterFactory(): ExporterFactoryInterface
    {
        return self::createExporterFactoryBuilder()->getExporterFactory();
    }

    public static function createActionFactoryBuilder(): ActionFactoryBuilderInterface
    {
        return new ActionFactoryBuilder();
    }

    public static function createExporterFactoryBuilder(): ExporterFactoryBuilderInterface
    {
        return new ExporterFactoryBuilder();
    }

    private function __construct()
    {
    }
}

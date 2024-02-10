<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryBuilder;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Action\ActionFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryBuilder;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\ExporterFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryBuilder;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryBuilderInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterFactoryInterface;

final class DataTables
{
    public static function createFilterFactory(): FilterFactoryInterface
    {
        return self::createFilterFactoryBuilder()->getFilterFactory();
    }

    public static function createActionFactory(): ActionFactoryInterface
    {
        return self::createActionFactoryBuilder()->getActionFactory();
    }

    public static function createExporterFactory(): ExporterFactoryInterface
    {
        return self::createExporterFactoryBuilder()->getExporterFactory();
    }

    public static function createFilterFactoryBuilder(): FilterFactoryBuilderInterface
    {
        return new FilterFactoryBuilder();
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

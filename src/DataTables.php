<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle;

final class DataTables
{
    public static function createDataTableFactory(): DataTableFactoryInterface
    {
        return self::createDataTableFactoryBuilder()->getDataTableFactory();
    }

    public static function createDataTableFactoryBuilder(): DataTableFactoryBuilderInterface
    {
        return new DataTableFactoryBuilder();
    }

    private function __construct()
    {
    }
}

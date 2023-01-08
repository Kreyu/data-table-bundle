<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Filter\FilterInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

class KreyuDataTableExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('columns.php');
        $loader->load('core.php');
        $loader->load('filters.php');
        $loader->load('personalization.php');
        $loader->load('twig.php');

        $container
            ->registerForAutoconfiguration(ColumnTypeInterface::class)
            ->addTag('kreyu_data_table.column_type');

        $container
            ->registerForAutoconfiguration(FilterInterface::class)
            ->addTag('kreyu_data_table.filter');

        $container
            ->registerForAutoconfiguration(DataTableTypeInterface::class)
            ->addTag('kreyu_data_table.type');
    }
}

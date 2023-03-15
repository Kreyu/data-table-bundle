<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class KreyuDataTableExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('columns.php');
        $loader->load('core.php');
        $loader->load('actions.php');
        $loader->load('exporter.php');
        $loader->load('extensions.php');
        $loader->load('filtration.php');
        $loader->load('personalization.php');
        $loader->load('twig.php');

        $config = $this->resolveConfiguration($configs, $container);

        $container
            ->registerForAutoconfiguration(DataTableTypeInterface::class)
            ->addTag('kreyu_data_table.type')
        ;

        $container
            ->registerForAutoconfiguration(DataTableTypeExtensionInterface::class)
            ->addTag('kreyu_data_table.type_extension')
        ;

        $container
            ->registerForAutoconfiguration(ColumnTypeInterface::class)
            ->addTag('kreyu_data_table.column.type')
        ;

        $container
            ->registerForAutoconfiguration(ColumnTypeExtensionInterface::class)
            ->addTag('kreyu_data_table.column.type_extension')
        ;

        $container
            ->registerForAutoconfiguration(FilterTypeInterface::class)
            ->addTag('kreyu_data_table.filter.type')
        ;

        $container
            ->registerForAutoconfiguration(FilterTypeExtensionInterface::class)
            ->addTag('kreyu_data_table.filter.type_extension')
        ;

        $container
            ->registerForAutoconfiguration(ExporterTypeInterface::class)
            ->addTag('kreyu_data_table.exporter.type')
        ;

        $container
            ->registerForAutoconfiguration(PersistenceAdapterInterface::class)
            ->addTag('kreyu_data_table.persistence.adapter')
        ;

        $container
            ->registerForAutoconfiguration(ProxyQueryFactoryInterface::class)
            ->addTag('kreyu_data_table.proxy_query.factory')
        ;

        $container
            ->getDefinition('kreyu_data_table.twig.data_table_extension')
            ->setArgument('$themes', $config['themes'])
        ;

        $container
            ->getDefinition('kreyu_data_table.type_extension.default_configuration')
            ->setArgument('$defaults', $config['defaults'])
        ;
    }

    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('framework')) {
            $container->prependExtensionConfig('framework', [
                'cache' => [
                    'pools' => [
                        'kreyu_data_table.persistence.cache.default' => [
                            'adapter' => 'cache.adapter.filesystem',
                        ],
                    ],
                ],
            ]);
        }
    }

    private function resolveConfiguration(array $configs, ContainerBuilder $container): array
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $serviceReferenceNodes = [
            'persistence_adapter',
            'persistence_subject_provider',
            'form_factory',
            'column_factory',
            'action_factory',
            'filter_factory',
            'exporter_factory',
            'request_handler',
        ];

        array_walk_recursive($config, function (&$item, $key) use ($serviceReferenceNodes) {
            if (in_array($key, $serviceReferenceNodes) && is_string($item)) {
                $item = new Reference($item);
            }
        });

        return $config;
    }
}

<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DependencyInjection;

use Kreyu\Bundle\DataTableBundle\Action\Extension\ActionTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Action\Type\ActionTypeInterface;
use Kreyu\Bundle\DataTableBundle\Column\Extension\ColumnTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Column\Type\ColumnTypeInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Extension\ExporterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Exporter\Type\ExporterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Extension\DataTableTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Extension\FilterTypeExtensionInterface;
use Kreyu\Bundle\DataTableBundle\Filter\Type\FilterTypeInterface;
use Kreyu\Bundle\DataTableBundle\Persistence\PersistenceAdapterInterface;
use Kreyu\Bundle\DataTableBundle\Query\ProxyQueryFactoryInterface;
use Kreyu\Bundle\DataTableBundle\Type\DataTableTypeInterface;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;

class KreyuDataTableExtension extends Extension implements PrependExtensionInterface
{
    private array $autoconfiguration = [
        DataTableTypeInterface::class => 'kreyu_data_table.type',
        DataTableTypeExtensionInterface::class => 'kreyu_data_table.type_extension',
        ColumnTypeInterface::class => 'kreyu_data_table.column.type',
        ColumnTypeExtensionInterface::class => 'kreyu_data_table.column.type_extension',
        FilterTypeInterface::class => 'kreyu_data_table.filter.type',
        FilterTypeExtensionInterface::class => 'kreyu_data_table.filter.type_extension',
        ActionTypeInterface::class => 'kreyu_data_table.action.type',
        ActionTypeExtensionInterface::class => 'kreyu_data_table.action.type_extension',
        ExporterTypeInterface::class => 'kreyu_data_table.exporter.type',
        ExporterTypeExtensionInterface::class => 'kreyu_data_table.exporter.type_extension',
        PersistenceAdapterInterface::class => 'kreyu_data_table.persistence.adapter',
        ProxyQueryFactoryInterface::class => 'kreyu_data_table.proxy_query.factory',
    ];

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
        $loader->load('filtration.php');
        $loader->load('pagination.php');
        $loader->load('personalization.php');
        $loader->load('twig.php');

        if ($container->getParameter('kernel.debug')) {
            $loader->load('debug.php');
        }

        $config = $this->resolveConfiguration($configs, $container);

        foreach ($this->autoconfiguration as $interface => $tag) {
            $container->registerForAutoconfiguration($interface)->addTag($tag);
        }

        $container
            ->getDefinition('kreyu_data_table.type.data_table')
            ->setArgument('$defaults', $config['defaults'])
        ;

        if ($container->getParameter('kernel.debug')) {
            $container
                ->getDefinition('kreyu_data_table.debug.data_collector')
                ->setArgument('$maxDepth', $config['profiler']['max_depth']);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasExtension('framework')) {
            $container->prependExtensionConfig('framework', [
                'cache' => [
                    'pools' => [
                        'kreyu_data_table.persistence.cache.default' => [
                            'adapter' => 'cache.adapter.filesystem',
                            'tags' => true,
                        ],
                    ],
                ],
            ]);
        }

        if ($this->isAssetMapperAvailable($container)) {
            $container->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__.'/../../assets/controllers' => '@kreyu/data-table-bundle',
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
            'column_visibility_group_builder',
            'request_handler',
        ];

        array_walk_recursive($config, function (&$item, $key) use ($serviceReferenceNodes) {
            if (in_array($key, $serviceReferenceNodes) && is_string($item)) {
                $item = new Reference($item);
            }
        });

        return $config;
    }

    private function isAssetMapperAvailable(ContainerBuilder $container): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        // check that FrameworkBundle 6.3 or higher is installed
        $bundlesMetadata = $container->getParameter('kernel.bundles_metadata');

        if (!isset($bundlesMetadata['FrameworkBundle'])) {
            return false;
        }

        return is_file($bundlesMetadata['FrameworkBundle']['path'].'/Resources/config/asset_mapper.php');
    }
}

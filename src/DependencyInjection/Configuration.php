<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kreyu_data_table');

        $treeBuilder->getRootNode()
            // TODO: Remove this whole validation block after removing the root "themes" node
            //       as it works as a backwards compatibility layer
            ->validate()
                ->always()
                ->then(function (array $config) {
                    if (empty($config['defaults']['themes']) && !empty($config['themes'])) {
                        $config['defaults']['themes'] = $config['themes'];
                    }

                    return $config;
                })
            ->end()
            ->children()
                ->arrayNode('themes')
                    ->scalarPrototype()->end()
                    ->setDeprecated(
                        'kreyu/data-table-bundle',
                        '0.12.0',
                        'The child node "%node%" at path "%path%" is deprecated, use "defaults.themes" instead.',
                    )
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('themes')
                            ->scalarPrototype()->end()
                            ->defaultValue(['@KreyuDataTable/themes/base.html.twig'])
                        ->end()
                        ->scalarNode('column_factory')
                            ->defaultValue('kreyu_data_table.column.factory')
                        ->end()
                        ->scalarNode('action_factory')
                            ->defaultValue('kreyu_data_table.action.factory')
                        ->end()
                        ->scalarNode('request_handler')
                            ->defaultValue('kreyu_data_table.request_handler.http_foundation')
                        ->end()
                        ->arrayNode('sorting')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultTrue()
                                ->end()
                                ->booleanNode('persistence_enabled')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('persistence_adapter')
                                    ->defaultNull()
                                ->end()
                                ->scalarNode('persistence_subject_provider')
                                    ->defaultNull()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('pagination')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultTrue()
                                ->end()
                                ->booleanNode('persistence_enabled')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('persistence_adapter')
                                    ->defaultNull()
                                ->end()
                                ->scalarNode('persistence_subject_provider')
                                    ->defaultNull()
                                ->end()
                                ->arrayNode('per_page_choices')
                                    ->integerPrototype()->end()
                                    ->defaultValue([10, 25, 50, 100])
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('filtration')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultTrue()
                                ->end()
                                ->booleanNode('persistence_enabled')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('persistence_adapter')
                                    ->defaultNull()
                                ->end()
                                ->scalarNode('persistence_subject_provider')
                                    ->defaultNull()
                                ->end()
                                ->scalarNode('filter_factory')
                                    ->defaultValue('kreyu_data_table.filter.factory')
                                ->end()
                                ->scalarNode('form_factory')
                                    ->defaultValue('form.factory')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('personalization')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultFalse()
                                ->end()
                                ->booleanNode('persistence_enabled')
                                    ->defaultFalse()
                                ->end()
                                ->scalarNode('persistence_adapter')
                                    ->defaultNull()
                                ->end()
                                ->scalarNode('persistence_subject_provider')
                                    ->defaultNull()
                                ->end()
                                ->scalarNode('form_factory')
                                    ->defaultValue('form.factory')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('exporting')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->booleanNode('enabled')
                                    ->defaultTrue()
                                ->end()
                                ->scalarNode('exporter_factory')
                                    ->defaultValue('kreyu_data_table.exporter.factory')
                                ->end()
                                ->scalarNode('form_factory')
                                    ->defaultValue('form.factory')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('profiler')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('max_depth')
                            ->defaultValue(3)
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

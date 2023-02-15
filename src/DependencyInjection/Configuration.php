<?php

declare(strict_types=1);

namespace Kreyu\Bundle\DataTableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Cache\CacheInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('kreyu_data_table');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('themes')
                    ->scalarPrototype()->end()
                    ->defaultValue(['@KreyuDataTable/themes/bootstrap_5.html.twig'])
                ->end()
                ->arrayNode('defaults')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('column_factory')
                            ->defaultValue('kreyu_data_table.column.factory')
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
                                    ->defaultValue($this->getDefaultPersistenceAdapter('sorting'))
                                ->end()
                                ->scalarNode('persistence_subject_provider')
                                    ->defaultValue($this->getDefaultPersistenceSubjectProvider())
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
                                    ->defaultValue($this->getDefaultPersistenceAdapter('pagination'))
                                ->end()
                                ->scalarNode('persistence_subject_provider')
                                    ->defaultValue($this->getDefaultPersistenceSubjectProvider())
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
                                    ->defaultValue($this->getDefaultPersistenceAdapter('filtration'))
                                ->end()
                                ->scalarNode('persistence_subject_provider')
                                    ->defaultValue($this->getDefaultPersistenceSubjectProvider())
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
                                    ->defaultValue($this->getDefaultPersistenceAdapter('personalization'))
                                ->end()
                                ->scalarNode('persistence_subject_provider')
                                    ->defaultValue($this->getDefaultPersistenceSubjectProvider())
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
                                    ->defaultValue(true)
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
            ->end()
        ;

        return $treeBuilder;
    }

    private function getDefaultPersistenceAdapter(string $context): ?string
    {
        if (class_exists(CacheInterface::class)) {
            return "kreyu_data_table.$context.persistence.adapter.cache";
        }

        return null;
    }

    private function getDefaultPersistenceSubjectProvider(): ?string
    {
        if (class_exists(TokenStorageInterface::class)) {
            return 'kreyu_data_table.persistence.subject_provider.token_storage';
        }

        return null;
    }

}

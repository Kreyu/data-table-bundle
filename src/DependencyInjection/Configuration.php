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
            ->children()
                ->scalarNode('theme')
                    ->defaultValue('@KreyuDataTable/themes/bootstrap_5.html.twig')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}

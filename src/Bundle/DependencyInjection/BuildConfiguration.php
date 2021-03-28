<?php

namespace EFrane\PharBuilder\Bundle\DependencyInjection;

use EFrane\PharBuilder\Config\Sections\Build;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @see Build
 */
class BuildConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $build = new TreeBuilder('build');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $build->getRootNode();

        $children = $rootNode
            ->addDefaultsIfNotSet()
            ->children();

        $children
            ->booleanNode('dump_container_debug_info')
            ->info('Will dump additional container variants in Yaml and Graphviz to help with debugging')
            ->defaultFalse();

        $children
            ->booleanNode('include_debug_commands')
            ->defaultFalse();

        $children
            ->scalarNode('temp_path')
            ->defaultValue('%kernel.project_dir%/var/phar');

        $children
            ->scalarNode('output_path')
            ->info('The resulting phar will be stored at this path')
            ->defaultValue('%kernel.project_dir%');

        $children
            ->scalarNode('output_filename')
            ->info('The resulting phar will have this filename (does not have to end with .phar)')
            ->cannotBeEmpty();

        $children
            ->scalarNode('environment')
            ->info('The application environment for the phar build')
            ->defaultValue('prod');

        $children
            ->booleanNode('debug')
            ->defaultFalse();

        return $build;
    }
}

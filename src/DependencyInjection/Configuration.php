<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\DependencyInjection;

use EFrane\PharBuilder\Application\PharApplication;
use EFrane\PharBuilder\Application\PharKernel;
use EFrane\PharBuilder\Config\Sections\Build;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('phar_builder');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('application_class')
            ->defaultValue(PharApplication::class)
            ->info('The application class')
            ->end()
            ->scalarNode('phar_kernel')
            ->defaultValue(PharKernel::class)
            ->info('The kernel used by the phar application')
            ->end()
            ->end();

        $this->addBuildConfiguration($treeBuilder);

        return $treeBuilder;
    }

    /**
     * @see Build
     */
    private function addBuildConfiguration(TreeBuilder $treeBuilder): void
    {
        $buildNodeChildren = $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('build')
            ->children();

        $buildNodeChildren
            ->booleanNode('dump_container_debug_info')
            ->info('Will dump additional container variants in Yaml and Graphviz to help with debugging')
            ->defaultFalse()
            ->end()
            ->booleanNode('include_debug_commands')
            ->defaultFalse()
            ->end()
            ->scalarNode('temp_path')
            ->defaultValue('%kernel.project_dir%/var/phar')
            ->end()
            ->scalarNode('output_path')
            ->info('The resulting phar will be stored at this path')
            ->defaultValue('%kernel.project_dir%')
            ->end()
            ->scalarNode('output_filename')
            ->info('The resulting phar will have this filename (does not have to end with .phar)')
            ->cannotBeEmpty()
            ->end()
            ->scalarNode('environment')
            ->info('The application environment for the phar build')
            ->defaultValue('prod')
            ->end()
            ->booleanNode('debug')
            ->defaultFalse()
            ->end();

        $buildNodeChildren->end();
    }
}

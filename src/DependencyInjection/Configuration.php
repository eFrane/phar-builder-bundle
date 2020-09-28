<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\DependencyInjection;


use EFrane\PharBuilder\Application\PharKernel;
use EFrane\PharBuilder\Development\Config\Build;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('phar_builder');

        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('phar_kernel')
                ->defaultValue(PharKernel::class)
                ->end()
            ->end();

        $this->addBuildConfiguration($treeBuilder);

        return $treeBuilder;
    }

    /**
     * @param TreeBuilder $treeBuilder
     * @see Build
     */
    private function addBuildConfiguration(TreeBuilder $treeBuilder)
    {
        $buildNodeChildren = $treeBuilder->getRootNode()
            ->children()
            ->arrayNode('build')
            ->children();

        $buildNodeChildren
            ->booleanNode('dump_container_debug_info')
            ->info('Will dump additonal container variants in Yaml and Graphviz to help with debugging')
            ->defaultFalse()
            ->end()
            ->booleanNode('include_debug_commands')
            ->defaultFalse()
            ->end()
            ->scalarNode('temp_path')
            ->defaultValue(sys_get_temp_dir())
            ->end()
            ->scalarNode('output_path')
            ->info('The resulting phar will be stored at this path')
            ->cannotBeEmpty()
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

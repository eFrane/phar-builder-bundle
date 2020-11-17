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
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\ScalarNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('phar_builder');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $children = $rootNode->children();

        $applicationClassDefinition = (new ScalarNodeDefinition('application_class'))
            ->defaultValue(PharApplication::class)
            ->info('The application class');
        $children->append($applicationClassDefinition);

        $pharKernelDefinition = (new ScalarNodeDefinition('phar_kernel'))
            ->defaultValue(PharKernel::class)
            ->info('The kernel used by the phar application');
        $children->append($pharKernelDefinition);

        $children->append($this->getBuildConfiguration());
        $children->append($this->getDependenciesConfiguration());

        return $treeBuilder;
    }

    /**
     * @see Build
     */
    private function getBuildConfiguration(): NodeDefinition
    {
        $build = $this->createSection('build');

        $children = $build->children();

        $children
            ->booleanNode('dump_container_debug_info')
            ->info('Will dump additional container variants in Yaml and Graphviz to help with debugging')
            ->defaultFalse()
            ->end();

        $children
            ->booleanNode('include_debug_commands')
            ->defaultFalse()
            ->end();

        $children
            ->scalarNode('temp_path')
            ->defaultValue('%kernel.project_dir%/var/phar')
            ->end();

        $children
            ->scalarNode('output_path')
            ->info('The resulting phar will be stored at this path')
            ->defaultValue('%kernel.project_dir%')
            ->end();

        $children
            ->scalarNode('output_filename')
            ->info('The resulting phar will have this filename (does not have to end with .phar)')
            ->cannotBeEmpty()
            ->end();

        $children
            ->scalarNode('environment')
            ->info('The application environment for the phar build')
            ->defaultValue('prod')
            ->end();

        $children
            ->booleanNode('debug')
            ->defaultFalse()
            ->end();

        return $build;
    }

    private function getDependenciesConfiguration(): NodeDefinition
    {
        $dependencies = $this->createSection('dependencies');

        $children = $dependencies->children();

        $children
            ->scalarNode('storage_dir')
            ->info('The location where depedencies like humbug/box are saved to')
            ->defaultValue($_SERVER['HOME'].'.phar-builder')
            ->end();

        return $dependencies;
    }

    private function createSection(string $name): ArrayNodeDefinition
    {
        return (new NodeBuilder())->arrayNode($name);
    }
}

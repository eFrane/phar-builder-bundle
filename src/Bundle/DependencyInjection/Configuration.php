<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Bundle\DependencyInjection;

use EFrane\PharBuilder\Application\PharApplication;
use EFrane\PharBuilder\Application\PharKernel;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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

        $pharKernelDefinition = (new ScalarNodeDefinition('kernel_class'))
            ->defaultValue(PharKernel::class)
            ->info('The kernel used by the phar application');
        $children->append($pharKernelDefinition);

        $children->append($this->appendConfig(BuildConfiguration::class));
        $children->append($this->appendConfig(DependenciesConfiguration::class));

        return $treeBuilder;
    }

    /**
     * @phpstan-param class-string $configClass
     */
    private function appendConfig(string $configClass): NodeDefinition
    {
        /** @var ConfigurationInterface $instance */
        $instance = new $configClass();

        return $instance->getConfigTreeBuilder()->getRootNode();
    }
}

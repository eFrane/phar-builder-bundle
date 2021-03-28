<?php

namespace EFrane\PharBuilder\Bundle\DependencyInjection;

use EFrane\PharBuilder\Config\Sections\Dependencies;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @see Dependencies
 */
class DependenciesConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $dependencies = new TreeBuilder('dependencies');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $dependencies->getRootNode();

        $children = $rootNode
            ->addDefaultsIfNotSet()
            ->children();

        $children
            ->scalarNode('storage_dir')
            ->info('The location where depedencies like humbug/box are saved to')
            ->defaultValue('%kernel.project_dir%/var/phar_builder');

        $versions = $children
            ->arrayNode('versions')
            ->info('List of packages and their required / supported versions')
            ->cannotBeOverwritten(true)
            ->useAttributeAsKey('name')
            ->defaultValue(
                [
                    'box'        => [
                        'vendor'  => 'box-project',
                        'name'    => 'box',
                        'version' => '~3.9.1',
                    ],
                    'php-scoper' => [
                        'vendor'  => 'humbug',
                        'name'    => 'php-scoper',
                        'version' => '0.14.*',
                    ],
                ]
            )
            ->arrayPrototype()
            ->children();

        $versions->scalarNode('vendor')
            ->info('Package vendor')
            ->cannotBeEmpty();

        $versions->scalarNode('name')
            ->info('Package name')
            ->cannotBeEmpty();

        $versions->scalarNode('version')
            ->info('Valid semver version')
            ->cannotBeEmpty()
            ->validate()
            ->ifTrue(
                static function ($value) {
                    $semverRegex = '/\s?((((\d+|\*)\.){1,3})(-[\da-z]+)?(?!.+-.+)|([\^~=](?!.+-.+))((\d+\.){1,3})(-[\da-z]+)?|(((\d+\.){1,3})(-[\da-z]+))?(\s+-?\s+)((\d+\.){1,3}(-[\da-z]+)?))\s?/i';

                    return is_string($value) && false !== preg_match($semverRegex, $value);
                }
            )
            ->then(
                static function ($value) {
                    return $value;
                }
            );

        return $dependencies;
    }
}

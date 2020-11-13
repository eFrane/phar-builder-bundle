<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\DependencyInjection;

use EFrane\PharBuilder\Development\Config\Config;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class PharBuilderExtension extends Extension
{
    /**
     * @param array<mixed,mixed> $configs
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('services.yaml');

        $configuration = new Configuration();
        $bundleConfiguration = $this->processConfiguration($configuration, $configs);

        // inject parsed bundle config into Configuration object
        $configurationObjectDefinition = $container->getDefinition(Config::class);
        $configurationObjectDefinition->setArgument(0, $bundleConfiguration);
    }

    /**
     * @param array<mixed,mixed> $config
     */
    public function getConfiguration(array $config, ContainerBuilder $container): Configuration
    {
        return new Configuration();
    }
}

<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\DependencyInjection;


use EFrane\PharBuilder\Development\Config\Config;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Twig\Environment;

class PharBuilderExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
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

        // $container->getDefinition(Environment::class)->addMethodCall('')
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new Configuration();
    }
}

<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Bundle;


use EFrane\PharBuilder\DependencyInjection\PharBuilderExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

class PharBuilderBundle extends Bundle
{
    public function boot()
    {
        $this->configureTwig();
    }

    private function configureTwig() {
        $bundleTemplatePath = $this->getPath().'/../Resources/templates';
        $bundleTemplateNamespace = 'PharBuilder';

        $twig = $this->container->get('twig');
        $originalLoader = $twig->getLoader();

        $bundleTwigLoader = new FilesystemLoader();

        $bundleTwigLoader->addPath($bundleTemplatePath, $bundleTemplateNamespace);

        if ($originalLoader instanceof ChainLoader) {
            $originalLoader->addLoader($bundleTwigLoader);
        } elseif ($originalLoader instanceof FilesystemLoader) {
            $originalLoader->addPath($bundleTemplatePath, $bundleTemplateNamespace);
        } else {
            $chainLoader = new ChainLoader();

            $chainLoader->addLoader($originalLoader);
            $chainLoader->addLoader($bundleTwigLoader);

            $twig->setLoader($chainLoader);
        }
    }

    protected function getContainerExtensionClass()
    {
        return PharBuilderExtension::class;
    }
}

<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Bundle;

use EFrane\PharBuilder\DependencyInjection\PharBuilderExtension;
use EFrane\PharBuilder\Exception\BundleSetupException;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

class PharBuilderBundle extends Bundle
{
    public function boot(): void
    {
        $this->configureTwig();
    }

    /**
     * Add the template path for the bundle templates to the Twig environment.
     *
     * @throws BundleSetupException if Twig cannot be configured
     */
    private function configureTwig(): void
    {
        $bundleTemplatePath = $this->getPath().'/../Resources/templates';
        $bundleTemplateNamespace = 'PharBuilder';

        /** @var Environment $twig */
        $twig = $this->container->get('twig');
        $originalLoader = $twig->getLoader();

        $bundleTwigLoader = new FilesystemLoader();

        try {
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
        } catch (LoaderError $e) {
            throw BundleSetupException::failedToConfigureTwig();
        }
    }

    protected function getContainerExtensionClass()
    {
        return PharBuilderExtension::class;
    }
}

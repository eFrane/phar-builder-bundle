<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Bundle;


use EFrane\PharBuilder\Application\PharCommandLoader;
use EFrane\PharBuilder\Bundle\PharBuilderBundle;
use Nyholm\BundleTest\AppKernel;
use Nyholm\BundleTest\BaseBundleTestCase;
use Symfony\Bundle\TwigBundle\TwigBundle;

class PharBuilderBundleTest extends BaseBundleTestCase
{
    public function testBundleWithDefaultConfiguration()
    {
        $kernel = $this->getBootedKernel('minimal_config');

        $container = $kernel->getContainer();

        $bundle = $container->get(PharCommandLoader::class);
        $this->assertInstanceOf(PharCommandLoader::class, $bundle);
    }

    protected function getBundleClass()
    {
        return PharBuilderBundle::class;
    }

    private function getBootedKernel(string $config): AppKernel
    {
        $kernel = $this->createKernel();

        $kernel->addBundle(TwigBundle::class);
        $kernel->addConfigFile(dirname(__DIR__).DIRECTORY_SEPARATOR."assets/{$config}.yml");

        $kernel->boot();

        return $kernel;
    }
}

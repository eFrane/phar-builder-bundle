<?php

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\CompilerPass\HidePharCommandsFromDefaultConsolePass;
// use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
//    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../../config/{packages}/*.yaml');
        $container->import('../../config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file(\dirname(__DIR__).'/../config/services.yaml')) {
            $container->import('../../config/{services}.yaml');
            $container->import('../../config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = \dirname(__DIR__).'/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    public function configureRoutes(RoutingConfigurator $routes)
    {
        // Do nothing, there are no routes
    }

    protected function build(ContainerBuilder $containerBuilder)
    {
        $containerBuilder->addCompilerPass(new HidePharCommandsFromDefaultConsolePass());
    }
}

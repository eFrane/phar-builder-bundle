<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Bundle;

use EFrane\PharBuilder\Bundle\DependencyInjection\PharBuilderExtension;
use EFrane\PharBuilder\Development\Process\IdentifiableProcessProviderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PharBuilderBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(IdentifiableProcessProviderInterface::class)
            ->addTag('phar_builder.process_provider');
    }

    protected function getContainerExtensionClass()
    {
        return PharBuilderExtension::class;
    }
}

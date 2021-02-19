<?php

declare(strict_types=1);

namespace EFrane\PharBuilder\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HidePharCommandsFromDefaultConsolePass implements CompilerPassInterface
{
    /**
     * Remove the console.command auto configuration tag from phar commands
     * to hide them from the default console (bin/console).
     */
    public function process(ContainerBuilder $container): void
    {
        $pharCommands = $container->findTaggedServiceIds('phar.command');

        foreach (array_keys($pharCommands) as $pharCommand) {
            $container->removeDefinition($pharCommand);
        }
    }
}

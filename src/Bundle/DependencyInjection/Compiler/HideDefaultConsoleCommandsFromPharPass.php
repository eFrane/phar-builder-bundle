<?php

declare(strict_types=1);

namespace EFrane\PharBuilder\Bundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HideDefaultConsoleCommandsFromPharPass implements CompilerPassInterface
{
    /**
     * Remove the console.command auto configuration tag from phar commands
     * to hide them from the default console (bin/console).
     */
    public function process(ContainerBuilder $container): void
    {
        $defaultCommands = $container->findTaggedServiceIds('console.command');

        foreach (array_keys($defaultCommands) as $defaultCommand) {
            $commandDefinition = $container->findDefinition($defaultCommand);

            if (!$commandDefinition->hasTag('phar.command')) {
                $container->removeDefinition($defaultCommand);
            }
        }
    }
}

<?php


namespace EFrane\PharBuilder\CompilerPass;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *
 *
 * @package EFrane\PharBuilder\CompilerPass
 */
class HidePharCommandsFromDefaultConsolePass implements CompilerPassInterface
{
    /**
     * Remove the console.command auto configuration tag from phar commands
     * to hide them from the default console (bin/console)
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container): void
    {
        $pharCommands = $container->findTaggedServiceIds('phar.command');

        foreach (array_keys($pharCommands) as $pharCommand) {
            $commandDefinition = $container->findDefinition($pharCommand);
            $commandDefinition->clearTag('console.command');
        }
    }
}

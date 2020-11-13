<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\CompilerPass;

use EFrane\PharBuilder\CompilerPass\HideDefaultConsoleCommandsFromPharPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class HideDefaultConsoleCommandsFromPharPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new HideDefaultConsoleCommandsFromPharPass());
    }

    public function testAreCommandsRemoved(): void
    {
        $testCommand = new class() extends Command {
            protected static $defaultName = 'pharbuilder:command:for:testing';
        };

        $testClassName = get_class($testCommand);
        $definition = new Definition($testClassName);
        $definition->addTag('console.command');

        $this->container->addDefinitions(['testcommand' => $definition]);

        $this->assertContainerBuilderHasService('testcommand');

        $this->compile();

        $this->assertContainerBuilderNotHasService('testcommand');
    }
}

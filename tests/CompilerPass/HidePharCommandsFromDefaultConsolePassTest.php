<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\CompilerPass;

use EFrane\PharBuilder\Command\PharCommand;
use EFrane\PharBuilder\CompilerPass\HidePharCommandsFromDefaultConsolePass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class HidePharCommandsFromDefaultConsolePassTest extends AbstractCompilerPassTestCase
{
    public function testAreCommandsRemoved(): void
    {
        $testCommand = new class() extends PharCommand {
            protected static $defaultName = 'pharbuilder:command:for:testing';
        };

        $testClassName = get_class($testCommand);
        $definition = new Definition($testClassName);

        $definition->addTag('console.command');
        $definition->addTag('phar.command');

        $this->container->addDefinitions(['testcommand' => $definition]);

        $this->assertContainerBuilderHasService('testcommand');

        $this->compile();

        $this->assertContainerBuilderNotHasService('testcommand');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new HidePharCommandsFromDefaultConsolePass());
    }
}

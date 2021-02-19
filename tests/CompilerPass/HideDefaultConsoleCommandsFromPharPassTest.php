<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\CompilerPass;

use EFrane\PharBuilder\Bundle\DependencyInjection\Compiler\HideDefaultConsoleCommandsFromPharPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HideDefaultConsoleCommandsFromPharPassTest extends AbstractCommandHidingTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new HideDefaultConsoleCommandsFromPharPass());
    }

    public function testAreCommandsRemoved(): void
    {
        $definition = $this->getDefaultCommandDefinition();

        $this->container->addDefinitions(['testCommand' => $definition]);

        self::assertContainerBuilderHasService('testCommand');

        $this->compile();

        self::assertContainerBuilderNotHasService('testCommand');
    }

    public function testKeepsPharCommands(): void
    {
        $defaultCommandDefinition = $this->getDefaultCommandDefinition();
        $pharCommandDefinition = $this->getPharCommandDefinition();

        $this->container->addDefinitions([
            'defaultCommand' => $defaultCommandDefinition,
            'pharCommand'    => $pharCommandDefinition,
        ]);

        self::assertContainerBuilderHasService('defaultCommand');
        self::assertContainerBuilderHasService('pharCommand');

        $this->compile();

        self::assertContainerBuilderNotHasService('defaultCommand');
        self::assertContainerBuilderHasService('pharCommand');
    }
}

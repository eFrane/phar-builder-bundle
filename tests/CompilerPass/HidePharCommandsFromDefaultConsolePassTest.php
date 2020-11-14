<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\CompilerPass;

use EFrane\PharBuilder\CompilerPass\HidePharCommandsFromDefaultConsolePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class HidePharCommandsFromDefaultConsolePassTest extends AbstractCommandHidingTestCase
{
    public function testAreCommandsRemoved(): void
    {
        $definition = $this->getPharCommandDefinition();

        $this->container->addDefinitions(['pharCommand' => $definition]);

        self::assertContainerBuilderHasService('pharCommand');

        $this->compile();

        self::assertContainerBuilderNotHasService('pharCommand');
    }

    public function testKeepsDefaultCommands(): void
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

        self::assertContainerBuilderHasService('defaultCommand');
        self::assertContainerBuilderNotHasService('pharCommand');
    }

    protected function registerCompilerPass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new HidePharCommandsFromDefaultConsolePass());
    }
}

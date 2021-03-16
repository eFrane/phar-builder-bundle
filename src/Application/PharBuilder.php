<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Manages refreshing the phar container and building the phar.
 */
class PharBuilder
{
    /**
     * @var PharContainerBuilder
     */
    private $containerBuilder;

    /**
     * @var BoxConfigurator
     */
    private $configurator;

    public function __construct(BoxConfigurator $configurator, PharContainerBuilder $containerBuilder)
    {
        $this->configurator = $configurator;
        $this->containerBuilder = $containerBuilder;
    }

    public function buildContainer(OutputInterface $output): void
    {
        $output->writeln('Prebuilding Application Container');

        $this->containerBuilder->build();
    }

    public function buildPhar(OutputInterface $output): void
    {
        if ($this->configurator->hasConfigurationDiverged()) {
            $output->writeln('<warning>Box configuration has diverged from the recommended defaults, consider running phar:dump:box again.</warning>');
        }

        $output->writeln('Running box compile');

        $runtimeConfig = $this->configurator->createRuntimeConfig();

        $buildProcess = new Process(
            [
                'vendor/bin/box',
                'compile',
                '-c',
                $runtimeConfig,
            ]
        );

        $buildProcess->setTimeout(0);
        $buildProcess->setTty(true);
        $buildProcess->mustRun();

        unlink($runtimeConfig);
    }
}

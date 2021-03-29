<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\Development\Process\BoxProcessProvider;
use Symfony\Component\Console\Output\OutputInterface;

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
    /**
     * @var BoxProcessProvider
     */
    private $boxProcessProvider;

    public function __construct(BoxConfigurator $configurator, BoxProcessProvider $boxProcessProvider, PharContainerBuilder $containerBuilder)
    {
        $this->configurator = $configurator;
        $this->boxProcessProvider = $boxProcessProvider;
        $this->containerBuilder = $containerBuilder;
    }

    public function buildContainer(OutputInterface $output): void
    {
        $output->writeln('Prebuilding Application Container');

        $this->containerBuilder->build();
    }

    public function buildPhar(OutputInterface $output, bool $debug): void
    {
        if ($this->configurator->hasConfigurationDiverged()) {
            $output->writeln('<warning>Box configuration has diverged from the recommended defaults, consider running phar:dump:box again.</warning>');
        }

        $output->writeln('Running box compile');

        $runtimeConfig = $this->configurator->createRuntimeConfig();

        $buildProcess = $this->boxProcessProvider->provide(
            [
                'compile',
                '-c',
                $runtimeConfig,
            ]
        );

        if ($debug) {
            $buildProcess->setTty(true);
        }

        $buildProcess->mustRun();

        unlink($runtimeConfig);
    }
}

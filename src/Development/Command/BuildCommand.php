<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;

use EFrane\PharBuilder\Application\BoxConfigurator;
use EFrane\PharBuilder\DependencyInjection\PharBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class BuildCommand extends Command
{
    protected static $defaultName = 'phar:build';
    /**
     * @var PharBuilder
     */
    private $builder;
    /**
     * @var BoxConfigurator
     */
    private $configurator;

    public function __construct(BoxConfigurator $configurator, PharBuilder $builder, string $name = null)
    {
        parent::__construct($name);
        $this->builder = $builder;
        $this->configurator = $configurator;
    }

    public function configure(): void
    {
        $this->setDescription('Build the phar');

        $this->addOption(
            'container-only',
            'C',
            InputOption::VALUE_NONE,
            'Only build the application container'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorReporting = error_reporting();
        error_reporting(E_ALL | E_STRICT);

        $retVal = Command::SUCCESS;

        $output = new SymfonyStyle($input, $output);

        if (!file_exists('box.json.dist')) {
            $output->error('Missing box.json, try running `bin/console phar:dump:box`');
            $output->writeln("Please make sure you're running bin/console from the repo root");

            return Command::FAILURE;
        }

        if ($this->configurator->hasConfigurationDiverged()) {
            $output->warning('Box configuration has diverged from the recommended defaults, consider running phar:dump:box again.');
        }

        try {
            $this->buildContainer($output);

            if (!$input->getOption('container-only')) {
                $this->buildPhar($output);
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());

            $retVal = Command::FAILURE;
        } finally {
            error_reporting($errorReporting);
        }

        return $retVal;
    }

    protected function buildPhar(OutputInterface $output): void
    {
        $output->writeln('Running vendor/bin/box compile');

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

    protected function buildContainer(OutputInterface $output): void
    {
        $output->writeln('Prebuilding Application Container');

        $this->builder->build();
    }
}

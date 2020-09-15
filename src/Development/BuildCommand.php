<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development;


use EFrane\PharBuilder\Application\PharKernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class BuildCommand extends Command
{
    protected static $defaultName = 'phar:build';

    public function configure(): void
    {
        $this->setDescription('Build the phar');

        $this->addOption(
            'only-container',
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

        if (!file_exists('box.json')) {
            $output->writeln("Please make sure you're running bin/console from the repo root");
            return Command::FAILURE;
        }

        try {
            $this->buildContainer($output);

            if (!$input->getOption('only-container')) {
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

    /**
     * @param OutputInterface $output
     */
    protected function buildPhar(OutputInterface $output): void
    {
        $output->writeln('Running vendor/bin/box compile');
        $buildProcess = new Process(['vendor/bin/box', 'compile']);
        $buildProcess->setTimeout(0);
        $buildProcess->setTty(true);
        $buildProcess->mustRun();
    }

    /**
     * @param OutputInterface $output
     */
    protected function buildContainer(OutputInterface $output): void
    {
        $output->writeln('Prebuilding Application Container');
        PharKernel::prebuildContainer('prod', false);
    }
}

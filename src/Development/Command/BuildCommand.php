<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;

use EFrane\PharBuilder\Application\BoxConfigurator;
use EFrane\PharBuilder\Application\PharBuilder;
use EFrane\PharBuilder\Development\Dependencies\DependencyManager;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class BuildCommand extends DependenciesUpdatingCommand
{
    protected static $defaultName = 'phar:build';

    /**
     * @var PharBuilder
     */
    private $pharBuilder;
    /**
     * @var BoxConfigurator
     */
    private $boxConfigurator;

    public function __construct(
        BoxConfigurator $boxConfigurator,
        DependencyManager $dependencyManager,
        PharBuilder $pharBuilder,
        string $name = null
    ) {
        parent::__construct($dependencyManager, $name);

        $this->boxConfigurator = $boxConfigurator;
        $this->pharBuilder = $pharBuilder;
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

        $this->addOption(
            'debug',
            '',
            InputOption::VALUE_NONE,
            'Debug build, enables the output from box'
        );

        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Force the build, i.e. force (re-)dumping the box config if required'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorReporting = error_reporting();
        error_reporting(E_ALL | E_STRICT);

        $retVal = Command::SUCCESS;

        $output = new SymfonyStyle($input, $output);

        try {
            $this->pharBuilder->buildContainer($output);

            if (!$input->getOption('container-only')) {
                $this->pharBuilder->buildPhar($output, (bool) $input->getOption('debug'));
            }
        } catch (Exception $e) {
            $output->writeln($e->getMessage());

            $retVal = Command::FAILURE;
        } finally {
            error_reporting($errorReporting);
        }

        return $retVal;
    }

    private function runBoxDump(OutputInterface $output): int
    {
        $application = null;
        if (null !== $this->getApplication()) {
            $dumpCommand = $this->getApplication()->get('phar:dump:box');

            return $dumpCommand->run(new ArgvInput([]), $output);
        }

        return Command::FAILURE;
    }
}

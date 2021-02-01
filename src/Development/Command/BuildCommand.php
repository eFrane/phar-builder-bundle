<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;

use EFrane\PharBuilder\Application\PharBuilder;
use EFrane\PharBuilder\Development\Dependencies\DependencyManager;
use Symfony\Component\Console\Command\Command;
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

    public function __construct(DependencyManager $dependencyManager, PharBuilder $pharBuilder, string $name = null)
    {
        parent::__construct($dependencyManager, $name);
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

        try {
            $this->pharBuilder->buildContainer($output);

            if (!$input->getOption('container-only')) {
                $this->pharBuilder->buildPhar($output);
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());

            $retVal = Command::FAILURE;
        } finally {
            error_reporting($errorReporting);
        }

        return $retVal;
    }
}

<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;

use EFrane\PharBuilder\Development\Dependencies\DependencyManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A base class for commands which handles keeping the external
 * dependencies up-to-date.
 */
abstract class DependenciesUpdatingCommand extends Command
{
    /**
     * @var DependencyManager
     */
    private $dependencyManager;

    public function __construct(DependencyManager $dependencyManager, string $name = null)
    {
        $this->dependencyManager = $dependencyManager;

        parent::__construct($name);

        $this->addOption(
            '--no-update-dependencies',
            '',
            InputOption::VALUE_NONE,
            'Disable the auto-update of external bundle dependencies'
        );
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        if (!$input->getOption('no-update-dependencies')) {
            $this->dependencyManager->updateDependenciesIfNecessary($output);
        }
    }
}

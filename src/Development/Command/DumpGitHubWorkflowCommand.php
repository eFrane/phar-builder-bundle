<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

class DumpGitHubWorkflowCommand extends Command
{
    public static $defaultName = 'phar:dump:github-workflow';

    public function __construct(Environment $twigEnvironment, string $name = null)
    {
        dd($twigEnvironment);
        parent::__construct($name);
    }


    protected function configure()
    {
        $this->setDescription('Dump a GitHub Workflow to build the Phar on versioned releases.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {


        return Command::SUCCESS;
    }
}

<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;

use EFrane\PharBuilder\Development\Config\Config;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

class DumpGitHubWorkflowCommand extends Command
{
    public static $defaultName = 'phar:dump:github-workflow';
    /**
     * @var Environment
     */
    private $twig;
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config, Environment $twig, string $name = null)
    {
        parent::__construct($name);

        $this->config = $config;
        $this->twig = $twig;
    }

    protected function configure(): void
    {
        $this->setDescription('Dump a GitHub Workflow to build the Phar on versioned releases.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $workflowTemplate = $this->twig->render(
            '@PharBuilder/github-workflow.yml.twig',
            [
                'phar_name' => $this->config->build()->getOutputFilename(),
            ]
        );

        $output->writeln($workflowTemplate);

        return Command::SUCCESS;
    }
}

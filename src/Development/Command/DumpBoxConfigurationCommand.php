<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DumpBoxConfigurationCommand extends Command
{
    public static $defaultName = 'phar:dump:box';
    /**
     * @var string|null
     */
    private $projectDir;

    public function __construct(string $projectDir = null, string $name = null)
    {
        parent::__construct($name);
        $this->projectDir = $projectDir;
    }

    public function configure()
    {
        $this->setDescription('Dump the configuration for Box');

        $this->addOption(
            'force',
            'f',
            InputOption::VALUE_NONE,
            'Force dumping even if a configuration file already exists'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new SymfonyStyle($input, $output);

        $hasBoxJson = file_exists($this->projectDir.'/box.json');
        $forceDump = $input->getOption('force');

        if ($hasBoxJson && !$forceDump) {
            $output->error('box.json already exists, use --force to overwrite');
            return Command::FAILURE;
        }

        if ($hasBoxJson && $forceDump) {
            $output->warning('Overwriting existing box.json');
        }

        return Command::SUCCESS;
    }
}

<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;


use EFrane\PharBuilder\Application\BoxConfigurator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

class DumpBoxConfigurationCommand extends Command
{
    public static $defaultName = 'phar:dump:box';

    /**
     * @var BoxConfigurator
     */
    private $configurator;

    public function __construct(BoxConfigurator $configurator, string $name = null)
    {
        parent::__construct($name);

        $this->configurator = $configurator;
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

        $fs = new Filesystem();

        $hasBoxJson = $fs->exists($this->configurator->getConfigPath());
        $forceDump = $input->getOption('force');

        if ($hasBoxJson && !$forceDump) {
            $output->error('box.json.dist already exists, use --force to overwrite');

            return Command::FAILURE;
        }

        if ($hasBoxJson && $forceDump) {
            $output->warning('Overwriting existing box.json.dist');
        }

        $this->configurator->dumpConfiguration();

        return Command::SUCCESS;
    }
}

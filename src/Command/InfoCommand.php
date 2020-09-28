<?php


namespace EFrane\PharBuilder\Command;


use EFrane\PharBuilder\Util\Path;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\ErrorHandler\Debug;

/**
 * Class InfoCommand
 * @package App
 */
class InfoCommand extends PharCommand implements DebugCommand
{
    protected static $defaultName = '_phar-debug:info';

    public function configure(): void
    {
        $this->setDescription('[debug] phar info');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("We're in a phar. This is great");

        return Command::SUCCESS;
    }
}

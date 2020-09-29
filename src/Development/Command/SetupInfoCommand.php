<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupInfoCommand extends Command
{
    public static $defaultName = 'phar:setup-info';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Info about bundle setup');

        return Command::SUCCESS;
    }
}

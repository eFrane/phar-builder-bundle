<?php

namespace EFrane\PharBuilder\Development\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Class TestCommand.
 *
 * Command to test the phar with different php versions
 */
class TestCommand extends Command
{
    protected static $defaultName = 'phar:test';

    public function configure(): void
    {
        $this->setDescription('Test run wrapper for different PHP Versions');

        $this->addArgument(
            'php_version',
            InputArgument::REQUIRED,
            'The PHP Version to test with'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $phpVersion = strval($input->getArgument('php_version'));

        $cmd = [
            'docker',
            'run',
            '--rm',
            '-it',
            sprintf('-v%s/build:/build', \getcwd()),
            sprintf('php:%s-cli-alpine', $phpVersion),
            '/build/test.phar',
        ];

        $process = new Process($cmd);

        $process->setTty(true);

        $process->run();

        return is_int($process->getExitCode()) ? $process->getExitCode() : self::FAILURE;
    }
}

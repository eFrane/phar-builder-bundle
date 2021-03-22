<?php

namespace EFrane\PharBuilder\Development\Process;

use EFrane\PharBuilder\Config\Config;
use EFrane\PharBuilder\Exception\PharBuildException;
use Symfony\Component\Process\Process;

/**
 * Build a command for Box with the correct location.
 */
final class BoxProcessProvider implements ProcessProvider
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function provide(array $processArguments): Process
    {
        $command = array_merge($this->getBoxCommand(), $processArguments);

        $process = new Process($command);

        $process->setTimeout(0);

        return $process;
    }

    /**
     * @return array<int,string>
     */
    private function getBoxCommand(): array
    {
        return [
            'php',
            sprintf($this->config->dependencies()->getStorageDir('box.phar')),
        ];
    }

    public function getVersion(): string
    {
        $process = $this->provide(['--version']);
        $process->enableOutput();
        $process->mustRun();

        $output = $process->getOutput();
        preg_match('/((\d+\.){2}\d+)(@[a-z0-9]{4,32})?/', $output, $matches);

        if (4 !== count($matches)) {
            throw PharBuildException::cannotDetermineBoxVersion();
        }

        return $matches[0];
    }
}

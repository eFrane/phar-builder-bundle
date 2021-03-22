<?php

namespace EFrane\PharBuilder\Development\Process;

use EFrane\PharBuilder\Config\Config;
use EFrane\PharBuilder\Exception\PharBuildException;
use Symfony\Component\Process\Process;

abstract class AbstractProcessProvider implements ProcessProviderInterface
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return array<int,string>
     */
    abstract protected function getToolCommand(): array;

    public function provide(array $processArguments): Process
    {
        $command = array_merge($this->getToolCommand(), $processArguments);

        $process = new Process($command);

        $process->setTimeout(0);

        return $process;
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

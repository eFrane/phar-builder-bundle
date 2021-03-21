<?php

namespace EFrane\PharBuilder\Development\Process;

use EFrane\PharBuilder\Config\Config;
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

    private function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function provide(array $processArguments): Process
    {
        $command = array_merge($this->getBoxCommand(), $processArguments);

        return new Process($command);
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
}

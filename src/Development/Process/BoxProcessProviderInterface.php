<?php

namespace EFrane\PharBuilder\Development\Process;

/**
 * Build a command for Box with the correct location.
 */
final class BoxProcessProviderInterface extends AbstractProcessProvider implements ProcessProviderInterface
{
    protected function getToolCommand(): array
    {
        return [
            'php',
            sprintf($this->config->dependencies()->getStorageDir('box.phar')),
        ];
    }
}

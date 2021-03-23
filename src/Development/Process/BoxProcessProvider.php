<?php

namespace EFrane\PharBuilder\Development\Process;

/**
 * Build a command for Box with the correct location.
 */
final class BoxProcessProvider extends AbstractProcessProviderProvider implements ProcessProviderInterface
{
    public function getName(): string
    {
        return 'box';
    }

    protected function getToolCommand(): array
    {
        return [
            'php',
            sprintf($this->config->dependencies()->getStorageDir('box.phar')),
        ];
    }
}

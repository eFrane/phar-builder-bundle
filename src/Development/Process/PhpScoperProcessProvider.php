<?php


namespace EFrane\PharBuilder\Development\Process;


final class PhpScoperProcessProvider extends AbstractProcessProvider implements ProcessProviderInterface
{
    protected function getToolCommand(): array
    {
        return [
            'php',
            $this->config->dependencies()->getStorageDir('php-scoper.phar'),
        ];
    }
}

<?php

declare(strict_types=1);

namespace EFrane\PharBuilder\Development\Process;

final class PhpScoperProcessProvider extends AbstractProcessProvider implements ProcessProviderInterface
{
    public function getName(): string
    {
        return 'php-scoper';
    }

    protected function getToolCommand(): array
    {
        return [
            'php',
            $this->config->dependencies()->getStorageDir('php-scoper.phar'),
        ];
    }
}

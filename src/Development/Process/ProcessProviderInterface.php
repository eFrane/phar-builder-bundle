<?php

declare(strict_types=1);

namespace EFrane\PharBuilder\Development\Process;

use Symfony\Component\Process\Process;

interface ProcessProviderInterface
{
    /**
     * @param array<int,string> $processArguments
     *
     * @phpstan-return Process<null>
     */
    public function provide(array $processArguments): Process;
}

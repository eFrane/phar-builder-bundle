<?php

namespace EFrane\PharBuilder\Development\Process;

use EFrane\PharBuilder\Exception\PharBuildException;
use Symfony\Component\Process\Process;

interface ProcessProviderInterface
{
    /**
     * @param array<int,string> $processArguments
     *
     * @phpstan-return Process<null>
     */
    public function provide(array $processArguments): Process;

    /**
     * @throws PharBuildException if the installed version cannot be determined
     */
    public function getVersion(): string;
}

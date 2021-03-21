<?php


namespace EFrane\PharBuilder\Development\Process;


use Symfony\Component\Process\Process;

interface ProcessProvider
{
    /**
     * @param array<int,string> $processArguments
     *
     * @phpstan-return Process<null>
     */
    public function provide(array $processArguments): Process;
}

<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\Config\Config;

/**
 * Generate a Phar stub invoking the Symfony Console Application with the correct config.
 */
final class StubGenerator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var mixed
     */
    private $projectDir;

    public function __construct(Config $config, string $projectDir)
    {
        $this->config = $config;

        $this->projectDir = $projectDir;
    }

    public function dump(): void
    {
        $stubCode = $this->generate();

        file_put_contents($this->getStubPath(), $stubCode);
    }

    public function getStubPath(): string
    {
        return $this->config->build()->getTempPath().'stub.php';
    }

    public function generate(): string
    {
        $containerPath = $this->config->build()->getTempPath(PharKernel::PHAR_CONTAINER_CACHE_DIR);

        if (0 === strpos($containerPath, $this->projectDir)) {
            $containerPath = substr($containerPath, strlen($this->projectDir) + 1);
        }

        return <<<STUB
#!/usr/bin/env php
<?php
declare(strict_types=1);
# This stub was generated with efrane/phar-builder-bundle

Phar::mapPhar();
require 'phar://'.__FILE__.'/.box/bin/check-requirements.php';
require 'phar://'.__FILE__.'/vendor/autoload.php';

\$bin = new EFrane\PharBuilder\Application\BinProvider(
    '{$containerPath}',
    {$this->config->getKernelClass()}::class,
    {$this->config->getApplicationClass()}::class
);

\$bin();

__HALT_COMPILER();
STUB;
    }
}

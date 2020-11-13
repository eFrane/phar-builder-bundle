<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\Development\Config\Config;
use Nette\PhpGenerator\PhpFile;
use Nette\PhpGenerator\PsrPrinter;

/**
 * Generate a Phar stub invoking the Symfony Console Application with the correct config.
 */
final class StubGenerator
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function generate(): string
    {
        $printer = new PsrPrinter();

        $file = new PhpFile();
        $file->setStrictTypes(true);
        $file->addComment('This stub was generated with efrane/phar-builder-bundle');

        $code = $printer->printFile($file);

        $code .= $this->getBinCode();

        return $this->getShellExecutionCommand().$code;
    }

    private function getBinCode(): string
    {
        $kernelClass = $this->config->getPharKernel();
        $applicationClass = $this->config->getApplicationClass();

        return <<<BIN_CODE
Phar::mapPhar();
require 'phar://'.__FILE__.'/vendor/autoload.php';

\$bin = new EFrane\PharBuilder\Application\BinProvider(
    $kernelClass::class, 
    $applicationClass::class
);

return \$bin();

\n__HALT_COMPILER();
BIN_CODE;
    }

    private function getShellExecutionCommand(): string
    {
        return "#!/usr/bin/env php\n";
    }
}

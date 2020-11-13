<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\Development\Config\Config;
use Twig\Environment;

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
     * @var Environment
     */
    private $twig;

    public function __construct(Config $config, Environment $twig)
    {
        $this->config = $config;
        $this->twig = $twig;
    }

    public function generate(): string
    {
        return $this->twig->render('@PharBuilder/stub.php.twig', [
            'kernelClass'      => $this->config->getPharKernel(),
            'applicationClass' => $this->config->getApplicationClass(),
        ]);
    }
}

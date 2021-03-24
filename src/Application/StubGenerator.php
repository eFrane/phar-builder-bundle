<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\Config\Config;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
     * @var mixed
     */
    private $projectDir;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Config $config, Environment $twig, string $projectDir)
    {
        $this->config = $config;
        $this->twig = $twig;

        $this->projectDir = $projectDir;
    }

    public function generate(): string
    {
        $containerPath = $this->config->build()->getTempPath(PharKernel::PHAR_CONTAINER_CACHE_DIR);
        $containerPath = substr($containerPath, strlen($this->projectDir) + 1);

        return $this->twig->render('@PharBuilder/stub.php.twig', [
            'containerPath'    => $containerPath,
            'kernelClass'      => $this->config->getPharKernel(),
            'applicationClass' => $this->config->getApplicationClass(),
        ]);
    }
}

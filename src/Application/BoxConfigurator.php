<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\Config\Config;
use EFrane\PharBuilder\Exception\PharBuildException;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class BoxConfigurator
{
    /**
     * @var string
     */
    protected $configPath;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var StubGenerator
     */
    private $stubGenerator;

    public function __construct(Config $config, ParameterBagInterface $parameterBag, StubGenerator $stubGenerator)
    {
        $this->config = $config;
        $this->configPath = $parameterBag->get('kernel.project_dir').'/box.json.dist';
        $this->basePath = $parameterBag->get('kernel.project_dir');
        $this->stubGenerator = $stubGenerator;
    }

    public function getConfigPath(): string
    {
        return $this->configPath;
    }

    public function dumpConfiguration(): void
    {
        $configuration = $this->getDefaultConfiguration();
        $configurationJson = json_encode($configuration, JSON_PRETTY_PRINT);

        file_put_contents($this->configPath, $configurationJson);
    }

    /**
     * @return array<string,mixed>
     */
    public function getDefaultConfiguration(): array
    {
        $basePath = $this->makePathRelative($this->basePath);
        $outputFilename = $this->makePathRelative($this->config->build()->getOutputPath($this->config->build()->getOutputFilename()));
        $stubPath = $this->makePathRelative($this->stubGenerator->getStubPath());
        $containerBuildPath = $this->makePathRelative($this->config->build()->getTempPath());

        return [
            'main'        => false,
            'base-path'   => $basePath,
            'output'      => $outputFilename,
            'stub'        => $stubPath,
            'finder'      => [
                [
                    'exclude'        => [
                        '.box',
                        '.git',
                        '.github',
                        '.idea',
                        'vendor-bin',
                        'var',
                    ],
                    'ignoreDotFiles' => false,
                    'in'             => '.',
                    'notName'        => [
                        '*.phar',
                        '.gitignore',
                        'box.json',
                        'scoper.inc.php',
                    ],
                    'followLinks'    => true,
                ],
                [
                    'in' => $containerBuildPath,
                ],
            ],
            'chmod'       => '0750',
        ];
    }

    public function hasConfigurationDiverged(): bool
    {
        if (!file_exists($this->configPath)) {
            return true;
        }

        $currentConfig = $this->loadConfig($this->configPath);
        $compareArr = array_diff_key($this->getDefaultConfiguration(), $currentConfig);

        return count($compareArr) === count($currentConfig);
    }

    /**
     * Merge user and system configuration for runtime.
     */
    public function createRuntimeConfig(): string
    {
        $fs = new Filesystem();

        $boxConfig = $this->loadConfig($this->configPath);
        if ($fs->exists('box.json')) {
            $userBoxConfig = $this->loadConfig('box.json');
            $boxConfig = array_merge($boxConfig, $userBoxConfig);
        }

        $json = json_encode($boxConfig, JSON_PRETTY_PRINT);

        if (false === $json) {
            throw new RuntimeException('Failed to dump config json');
        }

        $runtimeBoxJson = $this->config->build()->getTempPath('box.json');

        $fs->dumpFile($runtimeBoxJson, $json);

        return $runtimeBoxJson;
    }

    /**
     * @return array<string,mixed>
     */
    private function loadConfig(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $config = file_get_contents($path);

        if (false === $config) {
            return [];
        }

        return json_decode($config, true);
    }

    private function makePathRelative(string $path): string
    {
        $cwd = getcwd();
        if (!is_string($cwd)) {
            throw PharBuildException::cannotDetermineRuntimeDir();
        }

        if (Path::isBasePath($cwd, $path)) {
            $path = Path::makeRelative($path, $cwd);
        }

        if ('' === $path) {
            $path = '.';
        }

        return $path;
    }
}

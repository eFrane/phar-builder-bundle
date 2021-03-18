<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\Config\Config;
use phpDocumentor\Reflection\Types\True_;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

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

    public function getBoxPharPath(): string
    {
        return $this->config->dependencies()->getStorageDir().DIRECTORY_SEPARATOR.'box.phar';
    }

    public function dumpConfiguration(): void
    {
        $configuration = $this->getDefaultConfiguration();
        $configurationJson = json_encode($configuration, JSON_PRETTY_PRINT);

        if (!is_writable($this->configPath)) {
            mkdir($this->configPath, 0755, true);
        }

        file_put_contents($this->configPath, $configurationJson);

        $this->dumpStub();
    }

    /**
     * @return array<string,mixed>
     */
    public function getDefaultConfiguration(): array
    {
        return [
            'main'        => false,
            'base-path'   => $this->basePath,
            'output'      => $this->config->build()->getOutputPath().$this->config->build()->getOutputFilename(),
            'compression' => 'GZ',
            'stub'        => $this->config->build()->getTempPath().'stub.php',
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

        $boxConfig = $this->loadConfig('box.dist.json');
        if ($fs->exists('box.json')) {
            $userBoxConfig = $this->loadConfig('box.json');
            $boxConfig = array_merge($boxConfig, $userBoxConfig);
        }

        $tempConfig = $fs->tempnam(sys_get_temp_dir(), 'box.json');
        $json = json_encode($boxConfig, JSON_PRETTY_PRINT);

        if (false === $json) {
            throw new RuntimeException('Failed to dump config json');
        }

        $fs->dumpFile($tempConfig, $json);

        return $tempConfig;
    }

    private function dumpStub(): void
    {
        $stubCode = $this->stubGenerator->generate();

        file_put_contents($this->getStubPath(), $stubCode);
    }

    private function getStubPath(): string
    {
        return $this->config->build()->getTempPath().'stub.php';
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
}

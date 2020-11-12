<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;


use EFrane\PharBuilder\Development\Config\Config;
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

    public function dumpConfiguration(): void
    {
        $configuration = $this->getDefaultConfiguration();
        $configurationJson = json_encode($configuration, JSON_PRETTY_PRINT);

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

        $existingConfig = json_decode(file_get_contents($this->configPath), true);
        $compareArr = array_diff_key($this->getDefaultConfiguration(), $existingConfig);

        return count($compareArr) === count($existingConfig);
    }

    /**
     * Merge user and system configuration for runtime
     *
     * @return string
     */
    public function createRuntimeConfig(): string
    {
        $fs = new Filesystem();

        $boxConfig = json_decode(file_get_contents('box.json.dist'), true);
        if ($fs->exists('box.json')) {
            $userBoxConfig = json_decode(file_get_contents('box.json'), true);
            $boxConfig = array_merge($boxConfig, $userBoxConfig);
        }

        $tempConfig = $fs->tempnam(sys_get_temp_dir(), 'box.json');
        $fs->dumpFile($tempConfig, json_encode($boxConfig, JSON_PRETTY_PRINT));

        return $tempConfig;
    }

    private function dumpStub(): void
    {
        $stubCode = $this->stubGenerator->generate();

        file_put_contents($this->getStubPath(), $stubCode);
    }

    /**
     * @return string
     */
    private function getStubPath(): string
    {
        return $this->config->build()->getTempPath().'stub.php';
    }
}

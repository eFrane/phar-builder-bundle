<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Config\Sections;

use EFrane\PharBuilder\Config\ConfigSectionInterface;
use EFrane\PharBuilder\Config\Helper\GracefulDefaults;
use EFrane\PharBuilder\Config\Helper\HandlesCombinedPaths;

final class Build implements ConfigSectionInterface
{
    use GracefulDefaults;
    use HandlesCombinedPaths;

    /**
     * @var string
     */
    private $environment;
    /**
     * @var bool
     */
    private $debug;
    /**
     * @var bool
     */
    private $dumpContainerDebugInfo;
    /**
     * @var bool
     */
    private $includeDebugCommands;
    /**
     * @var string
     */
    private $tempPath;
    /**
     * @var string
     */
    private $outputPath;
    /**
     * @var string
     */
    private $outputFilename;

    public function setConfigFromArray(array $configArray): void
    {
        $this->debug = $this->default($configArray, 'debug', false);
        $this->dumpContainerDebugInfo = $this->default($configArray, 'dump_contaienr_debug_info', false);
        $this->environment = $this->default($configArray, 'environment', 'prod');
        $this->includeDebugCommands = $this->default($configArray, 'include_debug_commands', false);
        $this->outputPath = $this->required($configArray, 'output_path', 'build');
        $this->outputFilename = $this->required($configArray, 'output_filename', 'build');
        $this->tempPath = $this->required($configArray, 'temp_path', 'build');
    }

    public function getSectionName(): string
    {
        return 'build';
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    public function dumpContainerDebugInfo(): bool
    {
        return $this->dumpContainerDebugInfo;
    }

    public function includeDebugCommands(): bool
    {
        return $this->includeDebugCommands;
    }

    public function getTempPath(string $appendPath = ''): string
    {
        return $this->buildPath($this->tempPath, $appendPath);
    }

    public function getOutputPath(string $appendPath = ''): string
    {
        return $this->buildPath($this->outputPath, $appendPath);
    }

    public function getOutputFilename(): string
    {
        return $this->outputFilename;
    }
}

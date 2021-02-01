<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Config\Sections;

use EFrane\PharBuilder\Application\Util;
use EFrane\PharBuilder\Config\ConfigSectionInterface;

final class Build implements ConfigSectionInterface
{
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
        $this->debug = $configArray['debug'];
        $this->dumpContainerDebugInfo = $configArray['dump_container_debug_info'];
        $this->environment = $configArray['environment'];
        $this->includeDebugCommands = $configArray['include_debug_commands'];
        $this->outputPath = $configArray['output_path'];
        $this->outputFilename = $configArray['output_filename'];
        $this->tempPath = $configArray['temp_path'];

        if (!Util::endsWith($this->outputPath, DIRECTORY_SEPARATOR)) {
            $this->outputPath .= DIRECTORY_SEPARATOR;
        }

        if (!Util::endsWith($this->tempPath, DIRECTORY_SEPARATOR)) {
            $this->tempPath .= DIRECTORY_SEPARATOR;
        }
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

    public function getTempPath(): string
    {
        return $this->tempPath;
    }

    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    public function getOutputFilename(): string
    {
        return $this->outputFilename;
    }
}

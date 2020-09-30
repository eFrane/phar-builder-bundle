<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Config;


use EFrane\PharBuilder\Application\Util;

final class Build implements ConfigSectionInterface
{
    /**
     * @var string
     */
    protected $environment;
    /**
     * @var bool
     */
    protected $debug;
    /**
     * @var bool
     */
    protected $dumpContainerDebugInfo;
    /**
     * @var bool
     */
    protected $includeDebugCommands;
    /**
     * @var string
     */
    protected $tempPath;
    /**
     * @var string
     */
    protected $outputPath;
    /**
     * @var string
     */
    protected $outputFilename;

    public function setConfigFromArray(array $configArray): void
    {
        $this->debug = $configArray['debug'];
        $this->dumpContainerDebugInfo = $configArray['dump_container_debug_info'];
        $this->environment = $configArray['environment'];
        $this->includeDebugCommands = $configArray['include_debug_commands'];
        $this->outputPath = $configArray['output_path'];
        if (!Util::endsWith($this->outputPath, DIRECTORY_SEPARATOR)) {
            $this->outputPath .= DIRECTORY_SEPARATOR;
        }

        $this->outputFilename = $configArray['output_filename'];
        $this->tempPath = $configArray['temp_path'];
        if (!Util::endsWith($this->tempPath, DIRECTORY_SEPARATOR)) {
            $this->tempPath .= DIRECTORY_SEPARATOR;
        }
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function isDebug(): bool
    {
        return $this->debug;
    }

    /**
     * @return bool
     */
    public function dumpContainerDebugInfo(): bool
    {
        return $this->dumpContainerDebugInfo;
    }

    /**
     * @return bool
     */
    public function includeDebugCommands(): bool
    {
        return $this->includeDebugCommands;
    }

    /**
     * @return string
     */
    public function getTempPath(): string
    {
        return $this->tempPath;
    }

    /**
     * @return string
     */
    public function getOutputPath(): string
    {
        return $this->outputPath;
    }

    /**
     * @return string
     */
    public function getOutputFilename(): string
    {
        return $this->outputFilename;
    }
}

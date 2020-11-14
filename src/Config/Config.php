<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Config;

use EFrane\PharBuilder\Config\Sections\Build;
use EFrane\PharBuilder\Exception\ConfigurationException;

/**
 * Class Config.
 *
 * @method Build build()
 */
final class Config implements ConfigSectionInterface, ConfigSectionContainerInterface
{
    /**
     * @var string
     */
    protected $applicationClass;

    /**
     * @var ConfigSectionInterface[]|array<string,ConfigSectionInterface>
     */
    private $sections;

    /**
     * @var string
     */
    private $pharKernel;

    public function __construct()
    {
        $this->sections = [];

        $this->pharKernel = '';
        $this->applicationClass = '';
    }

    public function setConfigFromArray(array $configArray): void
    {
        $this->applicationClass = $configArray['application_class'];
        $this->pharKernel = $configArray['phar_kernel'];

        $sectionConfig = array_filter($configArray, 'is_array');

        if (0 < count($sectionConfig)) {
            $this->loadSectionConfigs($sectionConfig);
        }
    }

    public function registerSection(ConfigSectionInterface $section): void
    {
        $this->sections[$section->getSectionName()] = $section;
    }

    public function registerSections(iterable $sections): void
    {
        foreach ($sections as $section) {
            $this->registerSection($section);
        }
    }

    public function getSectionName(): string
    {
        return 'root';
    }

    public function getPharKernel(): string
    {
        return $this->pharKernel;
    }

    /**
     * Generic call method to allow access to config sections.
     *
     * @param string           $section The config section to be accessed
     * @param array<int,mixed> $args    The arguments passed to __call, will be ignored
     *
     * @return ConfigSectionInterface|mixed
     */
    public function __call(string $section, array $args)
    {
        if (0 !== count($args)) {
            throw ConfigurationException::mustNotPassArgumentsToSections();
        }

        return $this->get($section);
    }

    /**
     * @return ConfigSectionInterface|mixed
     */
    public function get(string $section)
    {
        if (!array_key_exists($section, $this->sections)) {
            throw ConfigurationException::configSectionNotFound($section);
        }

        return $this->sections[$section];
    }

    public function getApplicationClass(): string
    {
        return $this->applicationClass;
    }

    /**
     * @param array<string,array<string,mixed>> $sectionConfigs
     */
    private function loadSectionConfigs(array $sectionConfigs): void
    {
        foreach ($sectionConfigs as $sectionName => $sectionConfigArray) {
            if (!array_key_exists($sectionName, $this->sections)) {
                throw ConfigurationException::configSectionNotFound($sectionName);
            }

            $this->loadSectionConfig($sectionName, $sectionConfigArray);
        }
    }

    /**
     * @param array<string,mixed> $sectionConfigArray
     */
    private function loadSectionConfig(string $sectionName, array $sectionConfigArray): void
    {
        $this->sections[$sectionName]->setConfigFromArray($sectionConfigArray);
    }
}

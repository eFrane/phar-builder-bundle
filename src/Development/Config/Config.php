<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Config;

use EFrane\PharBuilder\Exception\ConfigurationException;

/**
 * Class Config.
 *
 * @method Build build()
 */
final class Config
{
    /**
     * @var string
     */
    protected $applicationClass;

    /**
     * @var ConfigSectionInterface[]|array<string,ConfigSectionInterface>
     */
    private $configSections;

    /**
     * @var string
     */
    private $pharKernel;

    /**
     * Config constructor.
     *
     * @param array<string,mixed> $bundleConfiguration
     */
    public function __construct(array $bundleConfiguration)
    {
        $this->applicationClass = $bundleConfiguration['application_class'];
        $this->pharKernel = $bundleConfiguration['phar_kernel'];

        $sections = array_filter($bundleConfiguration, 'is_array');

        $this->configSections = [];
        foreach ($sections as $sectionName => $sectionConfigArray) {
            $sectionConfigClass = __NAMESPACE__.'\\'.ucfirst($sectionName);

            if (!class_exists($sectionConfigClass)) {
                throw ConfigurationException::configSectionNotFound($sectionName);
            }

            /** @var ConfigSectionInterface $sectionConfig */
            $sectionConfig = new $sectionConfigClass();
            $sectionConfig->setConfigFromArray($sectionConfigArray);

            $this->configSections[$sectionName] = $sectionConfig;
        }
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
        // fall through for global config parameters
        if (method_exists($this, $section)) {
            return $this->$section();
        }

        if (0 !== count($args)) {
            throw ConfigurationException::mustNotPassArgumentsToSections();
        }

        if (!array_key_exists($section, $this->configSections)) {
            throw ConfigurationException::configSectionNotFound($section);
        }

        return $this->configSections[$section];
    }

    public function getApplicationClass(): string
    {
        return $this->applicationClass;
    }
}

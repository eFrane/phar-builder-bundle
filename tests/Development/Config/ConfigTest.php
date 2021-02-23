<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Development\Config;

use EFrane\PharBuilder\Bundle\DependencyInjection\Configuration;
use EFrane\PharBuilder\Config\Config;
use EFrane\PharBuilder\Config\ConfigSectionInterface;
use EFrane\PharBuilder\Config\Sections\Build;
use EFrane\PharBuilder\Config\Sections\Dependencies;
use EFrane\PharBuilder\Exception\ConfigurationException;
use EFrane\PharBuilder\Tests\TestCase;
use Symfony\Component\Config\Definition\Processor;

class ConfigTest extends TestCase
{
    public function testCreatesEmptyConfig(): void
    {
        $config = new Config();

        self::assertInstanceOf(Config::class, $config);
        self::assertCount(0, $this->getPropertyValue($config, 'sections'));
        self::assertEquals('root', $config->getSectionName());
        self::assertEmpty($config->getPharKernel());
        self::assertEmpty($config->getApplicationClass());
    }

    public function testLoadsConfigWithoutSections(): void
    {
        $config = new Config();

        $config->setConfigFromArray($this->getBaseConfigData());

        self::assertEquals('Phar\Application', $config->getApplicationClass());
        self::assertEquals('Kernel\Class', $config->getPharKernel());
    }

    public function testRegistersSection(): void
    {
        $config = new Config();

        self::assertCount(0, $this->getPropertyValue($config, 'sections'));
        $config->registerSections([$this->getTestSection()]);
        self::assertCount(1, $this->getPropertyValue($config, 'sections'));
    }

    public function testLoadsSectionData(): void
    {
        $config = new Config();

        $config->registerSections([$this->getTestSection()]);

        self::assertEquals(0, $config->get('test')->getFoo());

        $configData = array_merge(
            $this->getBaseConfigData(),
            ['test' => ['foo' => 42]]
        );
        $config->setConfigFromArray($configData);

        self::assertEquals(42, $config->get('test')->getFoo());
    }

    public function testFailsLoadingSectionDataOfMissingSection(): void
    {
        $config = new Config();

        $this->expectException(ConfigurationException::class);

        $configData = array_merge(
            $this->getBaseConfigData(),
            ['test' => ['foo' => 42]]
        );
        $config->setConfigFromArray($configData);
    }

    /**
     * The default configuration without any changes has missing values.
     */
    public function testFailsLoadingDefaultConfiguration(): void
    {
        $defaultConfig = $this->getMergedConfigurationData();

        $config = new Config();
        $config->registerSections([
            new Build(),
            new Dependencies(),
        ]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Required configuration value build.output_filename missing');

        $config->setConfigFromArray($defaultConfig);
    }

    /**
     * @after testFailsLoadingDefaultConfiguration
     */
    public function testLoadsMinimalWorkingConfiguration(): void
    {
        $defaultConfig = $this->getMergedConfigurationData([
            'phar_builder' => ['build' => ['output_filename' => 'outputfile']],
        ]);

        $config = new Config();
        $config->registerSections([
            new Build(),
            new Dependencies(),
        ]);

        $config->setConfigFromArray($defaultConfig);

        self::assertEquals('outputfile', $config->build()->getOutputFilename());
    }

    private function getTestSection(): ConfigSectionInterface
    {
        return new class() implements ConfigSectionInterface {
            /**
             * @var int
             */
            protected $foo;

            public function __construct()
            {
                $this->foo = 0;
            }

            public function getSectionName(): string
            {
                return 'test';
            }

            public function setConfigFromArray(array $configArray): void
            {
                $this->foo = $configArray['foo'];
            }

            public function getFoo(): int
            {
                return $this->foo;
            }
        };
    }

    /**
     * @return array<string,string>
     */
    private function getBaseConfigData(): array
    {
        return [
            'application_class' => 'Phar\Application',
            'phar_kernel'       => 'Kernel\Class',
        ];
    }

    /**
     * @param array<string,mixed> $testConfiguration
     *
     * @return array<string,mixed>
     */
    private function getMergedConfigurationData(array $testConfiguration = []): array
    {
        $configuration = new Configuration();
        $processor = new Processor();
        $config = $processor->processConfiguration($configuration, $testConfiguration);

        return $config;
    }
}

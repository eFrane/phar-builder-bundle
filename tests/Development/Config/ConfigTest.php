<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Development\Config;

use EFrane\PharBuilder\Development\Config\Config;
use EFrane\PharBuilder\Development\Config\ConfigSectionInterface;
use EFrane\PharBuilder\Tests\TestCase;

class ConfigTest extends TestCase
{
    public function testCreatesEmptyConfig(): void
    {
        $config = new Config();

        self::assertInstanceOf(Config::class, $config);
        self::assertCount(0, $this->getPropertyValue($config, 'configSections'));
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
}

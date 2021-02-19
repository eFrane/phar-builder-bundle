<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\DependencyInjection;

use EFrane\PharBuilder\Bundle\DependencyInjection\PharBuilderExtension;
use EFrane\PharBuilder\Config\Config;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class PharBuilderExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new PharBuilderExtension(),
        ];
    }

    public function testPassesConfigurationToBundleConfig(): void
    {
        $this->load();

        self::assertTrue($this->container->hasExtension('phar_builder'));

        $configDefinition = $this->container->getDefinition(Config::class);

        self::assertCount(2, $configDefinition->getMethodCalls());
    }
}

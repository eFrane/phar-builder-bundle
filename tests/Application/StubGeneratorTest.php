<?php

namespace EFrane\PharBuilder\Tests\Application;

use EFrane\PharBuilder\Application\StubGenerator;
use EFrane\PharBuilder\Development\Config\Config;
use EFrane\PharBuilder\Tests\TestCase;

class StubGeneratorTest extends TestCase
{
    /**
     * @var StubGenerator
     */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new StubGenerator(
            $this->getTestConfig(),
            $this->getTwig()
        );
    }

    public function testGeneratesValidPhp(): void
    {
        $res = $this->sut->generate();

        self::assertInstanceOf(StubGenerator::class, $this->sut);
        self::assertIsString($res);
        self::assertIsValidPHP($res);
    }

    /**
     * Build a dummy configuration for test purposes.
     */
    protected function getTestConfig(): Config
    {
        $config = new Config();

        $config->setConfigFromArray([
            'application_class' => 'TestApp\ApplicationClass',
            'phar_kernel'       => 'TestApp\PharKernel',
        ]);

        return $config;
    }
}

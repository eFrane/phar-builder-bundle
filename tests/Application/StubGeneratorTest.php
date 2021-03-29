<?php

namespace EFrane\PharBuilder\Tests\Application;

use EFrane\PharBuilder\Application\StubGenerator;
use EFrane\PharBuilder\Config\Config;
use EFrane\PharBuilder\Config\Sections\Build;
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
            'pro/ject/dir'
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
        $config->registerSection(new Build());

        $config->setConfigFromArray(
            [
                'application_class' => 'TestApp\ApplicationClass',
                'phar_kernel'       => 'TestApp\PharKernel',
                'build'             => ['output_filename' => 'foo', 'output_path' => 'foo', 'temp_path' => 'foo'],
            ]
        );

        return $config;
    }


}

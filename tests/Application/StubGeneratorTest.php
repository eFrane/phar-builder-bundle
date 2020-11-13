<?php

namespace EFrane\PharBuilder\Tests\Application;

use EFrane\PharBuilder\Application\StubGenerator;
use EFrane\PharBuilder\Tests\TestCase;

class StubGeneratorTest extends TestCase
{
    /**
     * @var StubGenerator
     */
    private $sut;

    public function setUp(): void
    {
        $config = $this->getTestConfig();

        $this->sut = new StubGenerator($config);
    }

    public function testInstance(): void
    {
        self::assertInstanceOf(StubGenerator::class, $this->sut);
    }

    public function testGenerate(): void
    {
        $res = $this->sut->generate();

        self::assertIsString($res);
        self::assertIsValidPHP($res);
    }
}

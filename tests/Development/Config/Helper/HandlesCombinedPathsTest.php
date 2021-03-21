<?php

namespace EFrane\PharBuilder\Tests\Development\Config\Helper;

use EFrane\PharBuilder\Config\Helper\HandlesCombinedPaths;
use EFrane\PharBuilder\Tests\TestCase;

class HandlesCombinedPathsTest extends TestCase
{
    /**
     * @var HandlesCombinedPaths|object
     */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new class() {
            use HandlesCombinedPaths;
        };
    }

    /**
     * @dataProvider providePathFormats
     */
    public function testFormats(string $expected, string $base, string $append): void
    {
        /* @phpstan-ignore-next-line */
        self::assertEquals($expected, $this->sut->buildPath($base, $append));
    }

    /**
     * @return array<int,array<int,string>>
     */
    public function providePathFormats(): array
    {
        return [
            ['base/', 'base', ''],
            ['base/', 'base/', ''],
            ['base/', 'base/', '/'],
            ['base/append', 'base', 'append'],
            ['base/append', 'base/', '/append'],
            ['base/append', 'base', '/append'],
        ];
    }
}

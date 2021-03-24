<?php

namespace EFrane\PharBuilder\Tests\Development\Config\Helper;

use EFrane\PharBuilder\Config\Helper\GracefulDefaults;
use EFrane\PharBuilder\Exception\ConfigurationException;
use EFrane\PharBuilder\Tests\TestCase;

class GracefulDefaultsTest extends TestCase
{
    /**
     * @var object|GracefulDefaults
     */
    private $sut;

    public function setUp(): void
    {
        $this->sut = new class() {
            use GracefulDefaults;
        };
    }

    /**
     * @return array<int,array<int,mixed>>
     */
    public function provideDefaultOptions(): array
    {
        return [
            [['option' => 'bar'], 'option', 'foo', 'bar'],
            [[], 'option', 'foo', 'foo'],
            [['baz' => 'bar'], 'option', 'foo', 'foo'],
        ];
    }

    /**
     * @param array<string,mixed> $options
     * @param mixed               $default
     * @param mixed               $expected
     *
     * @dataProvider provideDefaultOptions
     */
    public function testDefault(array $options, string $option, $default, $expected): void
    {
        /* @phpstan-ignore-next-line */
        $this->assertEquals($expected, $this->sut->default($options, $option, $default));
    }

    public function testRequiredFailsWithMissing(): void
    {
        $this->expectException(ConfigurationException::class);

        /* @phpstan-ignore-next-line */
        $this->sut->required([], 'required');
    }

    public function testRequiredFormatsMessageCorrectly(): void
    {
        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage(ConfigurationException::missingConfigurationValue('missing_value')->getMessage());

        /* @phpstan-ignore-next-line */
        $this->sut->required([], 'missing_value');

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage(ConfigurationException::missingConfigurationValue('section.missing_value')->getMessage());

        /* @phpstan-ignore-next-line */
        $this->sut->required([], 'missing_value', 'section');
    }
}

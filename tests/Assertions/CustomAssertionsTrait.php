<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Assertions;

trait CustomAssertionsTrait
{
    public static function isValidPHP(): IsValidPHP
    {
        return new IsValidPHP();
    }

    public static function assertIsValidPHP(string $code, string $message = ''): void
    {
        self::assertThat($code, self::isValidPHP(), $message);
    }
}

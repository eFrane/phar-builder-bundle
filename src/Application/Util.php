<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use Phar;

final class Util
{
    public static function pharRoot(): string
    {
        return Phar::running(true);
    }

    /**
     * @param mixed ...$vars
     */
    public static function phardd(...$vars): void
    {
        if (Util::inPhar()) {
            dd(...$vars);
        }
    }

    public static function inPhar(): bool
    {
        return '' !== Phar::running(false);
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);

        if (!$length) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }
}

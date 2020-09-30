<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;


use Phar;

final class Util
{
    public static function pharRoot()
    {
        return Phar::running(true);
    }

    public static function phardd(...$vars)
    {
        if (Util::inPhar()) {
            dd(...$vars);
        }
    }

    public static function inPhar()
    {
        return '' !== Phar::running(false);
    }

    public static function endsWith($haystack, $needle): bool
    {
        $length = strlen($needle);

        if (!$length) {
            return true;
        }

        return substr($haystack, -$length) === $needle;
    }
}

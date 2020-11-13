<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Exception;

use RuntimeException;

class PharBuildException extends RuntimeException
{
    public static function runningPhar(): self
    {
        throw new self('Cannot prebuild container in running phar');
    }
}

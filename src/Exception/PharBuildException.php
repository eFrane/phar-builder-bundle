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

    public static function cannotDetermineBoxVersion(): self
    {
        throw new self('Cannot determine Box version');
    }

    public static function unknownToolProcessName(string $name): self
    {
        throw new self("Unknown process tool: {$name}.");
    }

    public static function cannotDetermineRuntimeDir(): self
    {
        throw new self('Cannot determine runtime directory');
    }
}

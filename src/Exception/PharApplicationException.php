<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Exception;

use RuntimeException;

class PharApplicationException extends RuntimeException
{
    public static function missingCommandLoader(): self
    {
        return new self('Missing command loader');
    }

    public static function failedLoadingContainer(string $cachePath): self
    {
        return new self("Failed to load container at {$cachePath}");
    }
}

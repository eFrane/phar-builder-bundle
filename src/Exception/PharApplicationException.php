<?php
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
        return new self("Missing command loader");
    }

    public static function failedLoadingContainer(string $cachePath)
    {
        return new self("Failed to load container at {$cachePath}");
    }
}

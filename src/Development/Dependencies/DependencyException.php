<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Dependencies;

use RuntimeException;

class DependencyException extends RuntimeException
{
    public static function missingDataField(string $fieldName): self
    {
        return new self("Missing data field '{$fieldName}'");
    }

    public static function missingAssets(): self
    {
        return new self('Missing assets');
    }
}

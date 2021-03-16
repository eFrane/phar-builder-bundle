<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Exception;

use RuntimeException;

class ConfigurationException extends RuntimeException
{
    public static function configSectionNotFound(string $section): self
    {
        throw new self("Config section `{$section}` not found");
    }

    public static function mustNotPassArgumentsToSections(): self
    {
        throw new self('Must not pass arguments to sections');
    }

    public static function missingConfigurationValue(string $valueName): self
    {
        throw new self("Required configuration value {$valueName} is missing.");
    }
}

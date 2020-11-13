<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Exception;

use LogicException;

class BundleSetupException extends LogicException
{
    public static function failedToConfigureTwig(): self
    {
        return new self('Failed to configure Twig');
    }

    public static function missingBox(): self
    {
        return new self('Missing Box, try running `composer bin box require --dev humbug/box`');
    }
}

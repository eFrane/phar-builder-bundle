<?php
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
}

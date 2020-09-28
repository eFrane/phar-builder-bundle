<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Config;


interface ConfigSectionInterface
{
    public function setConfigFromArray(array $configArray): void;
}

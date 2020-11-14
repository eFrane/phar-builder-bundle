<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Config;

interface ConfigSectionInterface
{
    /**
     * @param array<string,mixed> $configArray
     */
    public function setConfigFromArray(array $configArray): void;

    public function getSectionName(): string;
}

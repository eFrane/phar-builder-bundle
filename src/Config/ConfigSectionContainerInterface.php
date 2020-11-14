<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Config;

interface ConfigSectionContainerInterface
{
    public function registerSection(ConfigSectionInterface $section): void;

    /**
     * @param iterable<ConfigSectionInterface> $sections
     */
    public function registerSections(iterable $sections): void;
}

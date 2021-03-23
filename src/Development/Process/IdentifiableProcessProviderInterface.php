<?php

declare(strict_types=1);

namespace EFrane\PharBuilder\Development\Process;

use EFrane\PharBuilder\Exception\PharBuildException;

/**
 * A concrete process provider can be identified by it's name and installed tool version.
 */
interface IdentifiableProcessProviderInterface extends ProcessProviderInterface
{
    public function getName(): string;

    /**
     * Parse the version in a semver compatible format from a symfony/console --version output.
     *
     * @throws PharBuildException if the installed version cannot be determined
     */
    public function getVersion(): string;
}

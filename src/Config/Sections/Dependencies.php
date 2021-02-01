<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Config\Sections;

use EFrane\PharBuilder\Config\ConfigSectionInterface;

final class Dependencies implements ConfigSectionInterface
{
    /**
     * @var string
     */
    private $storageDir;

    /**
     * @var array<string,array<string,string>>
     */
    private $versions;

    public function setConfigFromArray(array $configArray): void
    {
        $this->storageDir = $configArray['storage_dir'];
        $this->versions = $configArray['versions'];
    }

    public function getSectionName(): string
    {
        return 'dependencies';
    }

    public function getStorageDir(): string
    {
        return $this->storageDir;
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function getVersions(): array
    {
        return $this->versions;
    }
}

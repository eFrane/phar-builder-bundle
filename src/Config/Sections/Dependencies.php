<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Config\Sections;

use EFrane\PharBuilder\Config\ConfigSectionInterface;
use EFrane\PharBuilder\Config\Helper\GracefulDefaults;
use EFrane\PharBuilder\Config\Helper\HandlesCombinedPaths;

final class Dependencies implements ConfigSectionInterface
{
    use GracefulDefaults;
    use HandlesCombinedPaths;

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
        $this->storageDir = $this->required($configArray, 'storage_dir', 'dependencies');
        $this->versions = $this->required($configArray, 'versions', 'dependencies');
    }

    public function getSectionName(): string
    {
        return 'dependencies';
    }

    public function getStorageDir(string $appendPath = ''): string
    {
        return $this->buildPath($this->storageDir, $appendPath);
    }

    /**
     * @return array<string,array<string,string>>
     */
    public function getVersions(): array
    {
        return $this->versions;
    }
}

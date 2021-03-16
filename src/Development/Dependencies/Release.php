<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Dependencies;

class Release
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var array<int,DownloadUrl>
     */
    protected $downloadUrls;

    /**
     * @var string
     */
    private $vendor;

    /**
     * @param array<string,mixed> $releaseInformation
     */
    public function __construct(string $vendor, string $name, array $releaseInformation)
    {
        $this->name = $name;
        $this->vendor = $vendor;

        $this->version = $releaseInformation['tag_name'];

        if (!array_key_exists('assets', $releaseInformation) || 0 === count($releaseInformation['assets'])) {
            throw DependencyException::missingAssets();
        }

        $this->downloadUrls = [];
        foreach ($releaseInformation['assets'] as $asset) {
            if (!array_key_exists('browser_download_url', $asset)) {
                continue;
            }

            $this->downloadUrls[] = new DownloadUrl($asset['browser_download_url'], $asset['name']);
        }

        if (0 === count($this->downloadUrls)) {
            throw DependencyException::missingDataField('assets.browser_download_url');
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<int,DownloadUrl>|DownloadUrl[]
     */
    public function getDownloadUrls(): array
    {
        return $this->downloadUrls;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }
}

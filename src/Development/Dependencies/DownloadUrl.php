<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Dependencies;

final class DownloadUrl
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $storageName;

    public function __construct(string $url, string $storageName)
    {
        $this->url = $url;
        $this->storageName = $storageName;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getStorageName(): string
    {
        return $this->storageName;
    }
}

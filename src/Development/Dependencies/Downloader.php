<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Dependencies;

use EFrane\PharBuilder\Config\Config;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class Downloader
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(Config $config, HttpClientInterface $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    public function downloadAssets(Release $release): void
    {
        if (!is_dir($this->config->dependencies()->getStorageDir())) {
            $this->createStorageDir();
        }

        foreach ($release->getDownloadUrls() as $downloadUrl) {
            $response = $this->httpClient->request('GET', $downloadUrl->getUrl());

            $filename = $this->config->dependencies()->getStorageDir().DIRECTORY_SEPARATOR.$downloadUrl->getStorageName();
            $data = $response->getContent(false);

            file_put_contents($filename, $data);
            chmod($filename, 0755);
        }
    }

    private function createStorageDir(): void
    {
        (new Filesystem())->mkdir($this->config->dependencies()->getStorageDir());
    }
}

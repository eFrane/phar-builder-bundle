<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Development\Dependencies;

use EFrane\PharBuilder\Config\Config;
use EFrane\PharBuilder\Config\Sections\Dependencies;
use EFrane\PharBuilder\Development\Dependencies\Downloader;
use EFrane\PharBuilder\Development\Dependencies\Release;
use EFrane\PharBuilder\Tests\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class DownloaderTest extends TestCase
{
    public function testDownload(): void
    {
        $tempDir = sys_get_temp_dir();

        $configData = [
            'application_class' => 'TestApp\ApplicationClass',
            'phar_kernel'       => 'TestApp\PharKernel',
            'dependencies'      => [
                'storage_dir' => $tempDir,
                'versions'    => [
                    'flarb' => [
                        'vendor'  => 'floo',
                        'name'    => 'flarb',
                        'version' => '^3.0',
                    ],
                ],
            ],
        ];

        $config = new Config();
        $config->registerSection(new Dependencies());
        $config->setConfigFromArray($configData);

        $httpClient = new MockHttpClient(new MockResponse('http_client_response'));

        $release = new Release('floo/flarb', [
            'name'     => 'TestRelease',
            'tag_name' => '3.0',
            'assets'   => [
                [
                    'name'                 => uniqid('DownloadableAsset', false),
                    'browser_download_url' => 'http://i.am.not.a.real.domain',
                ],
            ],
        ]);

        $sut = new Downloader($config, $httpClient);

        $sut->downloadAssets($release);

        $filename = $tempDir.DIRECTORY_SEPARATOR.$release->getDownloadUrls()[0]->getStorageName();
        self::assertTrue(file_exists($filename));
        self::assertEquals('http_client_response', file_get_contents($filename));

        @unlink($filename);
    }
}

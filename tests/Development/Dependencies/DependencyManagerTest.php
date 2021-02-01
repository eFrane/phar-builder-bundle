<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Development\Dependencies;

use EFrane\PharBuilder\Config\Config;
use EFrane\PharBuilder\Config\Sections\Dependencies;
use EFrane\PharBuilder\Development\Dependencies\DependencyManager;
use EFrane\PharBuilder\Development\Dependencies\Downloader;
use EFrane\PharBuilder\Development\Dependencies\GitHubVersionDeterminator;
use EFrane\PharBuilder\Development\Dependencies\Release;
use EFrane\PharBuilder\Tests\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class DependencyManagerTest extends TestCase
{
    /**
     * @var DependencyManager
     */
    private $sut;

    /**
     * @var string
     */
    private $tempDir;

    public function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir();

        $configData = [
            'application_class' => 'TestApp\ApplicationClass',
            'phar_kernel'       => 'TestApp\PharKernel',
            'dependencies'      => [
                'storage_dir' => $this->tempDir,
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

        $downloaderHttpClient = new MockHttpClient(new MockResponse('flarb'));

        $downloader = new Downloader($config, $downloaderHttpClient);

        $ghData = json_encode([
            json_decode($this->getAsset('ghrelease.json')),
        ], JSON_THROW_ON_ERROR);

        $mockGHResponse = new MockResponse($ghData);

        $versionDeterminatorHttpClient = new MockHttpClient([$mockGHResponse]);

        $versionDeterminator = new GitHubVersionDeterminator($versionDeterminatorHttpClient);

        $this->sut = new DependencyManager($config, $downloader, $versionDeterminator);
    }

//    public function testUpdateDependenciesIfNecessary(): void
//    {
//        $output = new NullOutput();
//
//        $this->sut->updateDependenciesIfNecessary($output);
//    }

    public function testDetermineUpdatableDependencies(): void
    {
        $updatable = $this->sut->determineUpdatableDependencies();

        self::assertIsArray($updatable);
        self::assertCount(1, $updatable);
        self::assertEquals('3.9.0', $updatable[0]->getVersion());
    }

    public function testFetchLatestReleases(): void
    {
        $latestReleases = $this->sut->fetchLatestReleases();

        self::assertIsArray($latestReleases);
        self::assertCount(1, $latestReleases);
        self::assertInstanceOf(Release::class, $latestReleases[0]);
    }
}

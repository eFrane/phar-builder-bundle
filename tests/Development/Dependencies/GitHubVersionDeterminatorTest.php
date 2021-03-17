<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Development\Dependencies;

use EFrane\PharBuilder\Development\Dependencies\DependencyException;
use EFrane\PharBuilder\Development\Dependencies\GitHubVersionDeterminator;
use EFrane\PharBuilder\Development\Dependencies\Release;
use EFrane\PharBuilder\Tests\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class GitHubVersionDeterminatorTest extends TestCase
{
    public function testGetLatest(): void
    {
        $client = new MockHttpClient([$this->getMockResponseWithAssets()]);
        $sut = new GitHubVersionDeterminator($client);

        $latestRelease = $sut->getLatestReleaseWithPublishedAssets('floo', 'flarb');
        self::assertInstanceOf(Release::class, $latestRelease);
    }

    public function testThrowsWithoutAssets(): void
    {
        $this->expectException(DependencyException::class);
        $this->expectExceptionMessage('Tried the latest releases of \'floo/flarb\' and found no valid release');

        $client = new MockHttpClient([$this->getMockResponseWithoutAssets()]);
        $sut = new GitHubVersionDeterminator($client);

        $sut->getLatestReleaseWithPublishedAssets('floo', 'flarb');
    }

    public function testRetriesOnFailure(): void
    {
        $client = new MockHttpClient([$this->getMockResponseWithReleaseToSkip()]);
        $sut = new GitHubVersionDeterminator($client);

        $latestRelease = $sut->getLatestReleaseWithPublishedAssets('floo', 'flarb', 1);
        self::assertInstanceOf(Release::class, $latestRelease);
    }

    private function getMockResponseWithAssets(): MockResponse
    {
        $releaseWithAssetsJson = json_decode($this->getAsset('ghrelease.json'));

        return new MockResponse(
            json_encode(
                [
                    $releaseWithAssetsJson,
                ],
                JSON_THROW_ON_ERROR
            )
        );
    }

    private function getMockResponseWithoutAssets(): MockResponse
    {
        $releaseWithoutAssetsJson = json_decode($this->getAsset('ghrelease_noassets.json'));

        return new MockResponse(
            json_encode(
                [
                    $releaseWithoutAssetsJson,
                ],
                JSON_THROW_ON_ERROR
            )
        );
    }

    private function getMockResponseWithReleaseToSkip(): MockResponse
    {
        $releaseWithAssetsJson = json_decode($this->getAsset('ghrelease.json'));
        $releaseWithoutAssetsJson = json_decode($this->getAsset('ghrelease_noassets.json'));

        return new MockResponse(
            json_encode(
                [
                    $releaseWithoutAssetsJson,
                    $releaseWithAssetsJson,
                ],
                JSON_THROW_ON_ERROR
            )
        );
    }
}

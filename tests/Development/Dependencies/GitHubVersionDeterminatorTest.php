<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Development\Dependencies;

use EFrane\PharBuilder\Development\Dependencies\GitHubVersionDeterminator;
use EFrane\PharBuilder\Development\Dependencies\Release;
use EFrane\PharBuilder\Tests\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class GitHubVersionDeterminatorTest extends TestCase
{
    /**
     * @var GitHubVersionDeterminator
     */
    private $sut;

    public function setUp(): void
    {
        $data = json_encode([
            json_decode($this->getAsset('ghrelease.json')),
        ], JSON_THROW_ON_ERROR);

        $mockGHResponse = new MockResponse($data);

        $client = new MockHttpClient([$mockGHResponse]);

        $this->sut = new GitHubVersionDeterminator($client);
    }

    public function testGetLatestRelease(): void
    {
        $latestRelease = $this->sut->getLatestRelease('floo/flarb');
        self::assertInstanceOf(Release::class, $latestRelease);
    }
}

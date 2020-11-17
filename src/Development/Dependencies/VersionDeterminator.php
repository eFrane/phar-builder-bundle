<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Dependencies;

use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Fetches the latest GitHub Release of a given dependency.
 */
class VersionDeterminator
{
    /**
     * @var HttpClientInterface
     */
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @param string $owner      A GitHub username
     * @param string $repository A public GitHub repository name under $owner
     */
    public function getLatestRelease(string $owner, string $repository): Release
    {
        $releases = $this->getReleases($owner, $repository);

        // TODO: throw exception if release count is < 1

        return new Release($releases[0]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return array<int,array<string,mixed>>
     */
    private function getReleases(string $owner, string $repository): array
    {
        $url = sprintf('https://api.github.com/repos/%s/%s/releases', $owner, $repository);
        $response = $this->makeGitHubRequest($url);

        return json_decode($response->getContent(false), true);
    }

    private function makeGitHubRequest(string $url): ResponseInterface
    {
        return $this->httpClient->request('GET', $url, [
            'headers' => [
                'Accept' => 'application/vnd.github.v3+json',
            ],
        ]);
    }
}

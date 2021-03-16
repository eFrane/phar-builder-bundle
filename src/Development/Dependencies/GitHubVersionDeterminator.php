<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Dependencies;

use EFrane\PharBuilder\Exception\ConfigurationException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Fetches the latest GitHub Release of a given dependency.
 */
class GitHubVersionDeterminator
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
     * @param string $vendor A public GitHub username
     * @param string name A repository of that user
     */
    public function getLatestRelease(string $vendor, string $name): Release
    {
        $releases = $this->getReleases($vendor, $name);

        // TODO: throw exception if release count is < 1

        return new Release($vendor, $name, $releases[0]);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return array<int,array<string,mixed>>
     */
    private function getReleases(string $vendor, string $name): array
    {
        $url = sprintf('https://api.github.com/repos/%s/%s/releases', $vendor, $name);
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

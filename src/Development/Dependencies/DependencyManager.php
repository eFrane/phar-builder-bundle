<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Dependencies;

use Composer\Semver\Comparator;
use EFrane\PharBuilder\Config\Config;
use EFrane\PharBuilder\Development\Process\ProcessSelector;
use EFrane\PharBuilder\Exception\PharBuildException;
use Symfony\Component\Console\Output\OutputInterface;

class DependencyManager
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Downloader
     */
    private $downloader;

    /**
     * @var GitHubVersionDeterminator
     */
    private $versionDeterminator;
    /**
     * @var ProcessSelector
     */
    private $processSelector;

    public function __construct(Config $config, Downloader $downloader, GitHubVersionDeterminator $versionDeterminator, ProcessSelector $processSelector)
    {
        $this->config = $config;
        $this->downloader = $downloader;
        $this->processSelector = $processSelector;
        $this->versionDeterminator = $versionDeterminator;
    }

    public function updateDependenciesIfNecessary(?OutputInterface $output): void
    {
        $dependenciesToUpdate = $this->determineUpdatableDependencies();

        $count = count($dependenciesToUpdate);
        if (0 === $count) {
            return;
        }

        $this->tryWriteln(sprintf('Updating %d dependenc%s', $count, (1 < $count || 0 == $count) ? 'ies' : 'y'), $output);

        foreach ($dependenciesToUpdate as $dependencyToUpdate) {
            $this->tryWriteln('Updating '.$dependencyToUpdate->getName(), $output);

            $this->downloader->downloadAssets($dependencyToUpdate);
        }
    }

    /**
     * @return array<int,Release>|Release[]
     */
    public function determineUpdatableDependencies(): array
    {
        $latestReleases = $this->fetchLatestReleases();

        $updatable = [];

        foreach ($latestReleases as $latestRelease) {
            if ($this->isUpdatableRelease($latestRelease)) {
                $updatable[] = $latestRelease;
            }
        }

        return $updatable;
    }

    /**
     * @return array<int,Release>|Release[]
     */
    public function fetchLatestReleases(): array
    {
        return array_values(array_map(
            /**
             * @param array<string,string> $version
             */
            function (array $version) {
                return $this->versionDeterminator->getLatestReleaseWithPublishedAssets($version['vendor'], $version['name']);
            },
            $this->config->dependencies()->getVersions()
        ));
    }

    private function tryWriteln(string $line, ?OutputInterface $output): void
    {
        if ($output instanceof OutputInterface) {
            $output->writeln($line);
        }
    }

    private function getConfiguredVersionSelectorForRelease(Release $latestRelease): string
    {
        return $this->config->dependencies()->getVersions()[$latestRelease->getName()]['version'];
    }

    private function isUpdatableRelease(Release $latestRelease): bool
    {
        $requiredVersion = $this->getConfiguredVersionSelectorForRelease($latestRelease);

        $hasNewerVersion = Comparator::lessThanOrEqualTo($requiredVersion, $latestRelease->getVersion());

        try {
            $processProvider = $this->processSelector->get($latestRelease->getName());
            $currentVersion = $processProvider->getVersion();

            $hasNewerVersion = Comparator::greaterThan($latestRelease->getVersion(), $currentVersion);
        } catch (PharBuildException $e) {
            // This is fine, it's highly likely that box just isn't installed yet
        }

        return $hasNewerVersion;
    }
}

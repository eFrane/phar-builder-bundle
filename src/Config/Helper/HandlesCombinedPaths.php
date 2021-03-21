<?php


namespace EFrane\PharBuilder\Config\Helper;


trait HandlesCombinedPaths
{
    public function buildPath(string $basePath, string $appendPath): string
    {
        if (DIRECTORY_SEPARATOR === $basePath[-1]) {
            $basePath = substr($basePath, 0, -1);
        }

        if ('' === $appendPath) {
            $appendPath = DIRECTORY_SEPARATOR;
        }

        if (strlen($appendPath) && DIRECTORY_SEPARATOR === $appendPath[0]) {
            $appendPath = substr($appendPath, 1);
        }

        return sprintf('%s%s%s', $basePath, DIRECTORY_SEPARATOR, $appendPath);
    }
}

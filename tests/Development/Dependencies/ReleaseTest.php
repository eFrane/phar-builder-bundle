<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Development\Dependencies;

use EFrane\PharBuilder\Development\Dependencies\DependencyException;
use EFrane\PharBuilder\Development\Dependencies\Release;
use EFrane\PharBuilder\Tests\TestCase;

class ReleaseTest extends TestCase
{
    private const TEST_NAME = 'name';
    private const TEST_VENDOR = 'vendor';

    public function testReadsData(): void
    {
        $data = $this->getBaseData();

        $sut = new Release(self::TEST_VENDOR, self::TEST_NAME, $data);

        self::assertCount(1, $sut->getDownloadUrls());
    }

    public function testThrowsWithoutAssets(): void
    {
        $data = $this->getBaseData();
        unset($data['assets']);

        self::expectException(DependencyException::class);

        new Release(self::TEST_VENDOR, self::TEST_NAME, $data);
    }

    public function testThrowsWithoutDownloadUrls(): void
    {
        $data = $this->getBaseData();
        unset($data['assets'][0]['browser_download_url']);

        self::expectException(DependencyException::class);

        new Release(self::TEST_VENDOR, self::TEST_NAME, $data);
    }

    /**
     * @return array<string,mixed>
     */
    private function getBaseData(): array
    {
        return json_decode($this->getAsset('ghrelease.json'), true);
    }
}

<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\Development\Dependencies;

use EFrane\PharBuilder\Development\Dependencies\DownloadUrl;
use EFrane\PharBuilder\Tests\TestCase;

class DownloadUrlTest extends TestCase
{
    public function testDTO(): void
    {
        $sut = new DownloadUrl('foo', 'flarb');
        self::assertEquals('foo', $sut->getUrl());
        self::assertEquals('flarb', $sut->getStorageName());
    }
}

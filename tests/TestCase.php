<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests;

use EFrane\PharBuilder\Development\Config\Config;
use EFrane\PharBuilder\Tests\Assertions\CustomAssertionsTrait;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use CustomAssertionsTrait;

    /**
     * Build a dummy configuration for test purposes.
     */
    protected function getTestConfig(): Config
    {
        return new Config([
            'application_class' => 'TestApp\ApplicationClass',
            'phar_kernel'       => 'TestApp\PharKernel',
        ]);
    }

    /**
     * Manually configure the Twig root for tests.
     */
    protected function getTwig(): Environment
    {
        $loader = new FilesystemLoader();
        /* @noinspection PhpUnhandledExceptionInspection */
        $loader->addPath(
            dirname(__DIR__, 1).DIRECTORY_SEPARATOR.'src/Resources/templates',
            'PharBuilder'
        );

        return new Environment($loader);
    }
}

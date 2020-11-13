<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests;

use EFrane\PharBuilder\Development\Config\Config;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
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
}

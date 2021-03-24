<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests;

use EFrane\PharBuilder\Tests\Assertions\CustomAssertionsTrait;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use CustomAssertionsTrait;

    /**
     * @return mixed
     */
    protected function getPropertyValue(object $object, string $propertyName)
    {
        $reflectionClass = new \ReflectionClass($object);

        $reflectionProperty = $reflectionClass->getProperty($propertyName);
        $reflectionProperty->setAccessible(true);

        return $reflectionProperty->getValue($object);
    }

    protected function getAsset(string $path): string
    {
        $path = __DIR__.DIRECTORY_SEPARATOR.'assets'.DIRECTORY_SEPARATOR.$path;
        $contents = file_get_contents($path);

        if (false === $contents) {
            throw new \RuntimeException('File not found: '.$path);
        }

        return $contents;
    }
}

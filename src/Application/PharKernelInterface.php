<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;


use Symfony\Component\Config\ConfigCache;
use Symfony\Component\HttpKernel\KernelInterface;

interface PharKernelInterface extends KernelInterface
{
    public function getConfigCache(bool $debug): ConfigCache;
    public function isInBuild(): bool;
    public function setInBuild(bool $inBuild): void;
}

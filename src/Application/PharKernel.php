<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;


use RuntimeException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use function is_dir;
use function mkdir;
use function sys_get_temp_dir;

class PharKernel extends Kernel
{
    const PHAR_CONTAINER_CACHE_DIR = 'phar_container/';

    /**
     * @var bool Is the Phar currently being built
     */
    private $inBuild = false;

    /**
     * Returns a loader for the container.
     *
     * @param ContainerInterface|ContainerBuilder $container
     * @return DelegatingLoader The loader
     */
//    public function getContainerLoader(ContainerInterface $container)
//    {
//        $locator = new FileLocator($this);
//        $resolver = new LoaderResolver([
//            new XmlFileLoader($container, $locator),
//            new YamlFileLoader($container, $locator),
//            new IniFileLoader($container, $locator),
//            new PhpFileLoader($container, $locator),
//            new GlobFileLoader($container, $locator),
//            new DirectoryLoader($container, $locator),
//            new ClosureLoader($container),
//        ]);
//
//        return new DelegatingLoader($resolver);
//    }

    public function getKernelParameters(): array
    {
        return parent::getKernelParameters();
    }

    public function getCacheDir()
    {
        $runtimeDir = $this->prepareRuntimeDir();

        return $runtimeDir.DIRECTORY_SEPARATOR.'cache';
    }

    private function prepareRuntimeDir(): string
    {
        $runtimeDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'test_phar_runtime';

        if (!is_dir($runtimeDir) && !mkdir($runtimeDir) && !is_dir($runtimeDir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $runtimeDir));
        }

        return $runtimeDir;
    }

    public function getLogDir()
    {
        $runtimeDir = $this->prepareRuntimeDir();

        return $runtimeDir.DIRECTORY_SEPARATOR.'log';
    }

    public function getProjectDir(): string
    {
        $projectDir = parent::getProjectDir();

        if ($this->isInBuild()) {
            return '.';
        }

        if (Util::inPhar()) {
            return Util::pharRoot();
        }

        return $projectDir;
    }

    protected function initializeContainer()
    {
        if (Util::inPhar()) {
            $this->loadPrebuiltContainer();

            return;
        }

        parent::initializeContainer();
    }

    protected function loadPrebuiltContainer(): void
    {
        $configCache = $this->getConfigCache($this->isDebug());
        $cachePath = $configCache->getPath().'ProjectServiceContainer.php';

        $errorLevel = error_reporting(E_ALL ^ E_WARNING);

        try {
            /** @noinspection PhpIncludeInspection */
            if (!is_file($cachePath) || !is_object($this->container = include $cachePath)) {
                throw new RuntimeException("Failed to load container at {$cachePath}");
            }

            $this->container->set('kernel', $this);

            error_reporting($errorLevel);

            return;
        } catch (\Throwable $e) {
        }
    }

    /**
     * @param bool $debug
     * @return ConfigCache
     */
    public function getConfigCache(bool $debug): ConfigCache
    {
        $path = 'build'.DIRECTORY_SEPARATOR.self::PHAR_CONTAINER_CACHE_DIR;

        if (Util::inPhar()) {
            $path = Util::pharRoot().DIRECTORY_SEPARATOR.$path;
        }

        if ($this->isInBuild()) {
            // reset pre-built container during build
            $fs = new Filesystem();
            if ($fs->exists($path)) {
                $fs->remove(glob($path));
            }

            $fs->mkdir($path);
        }

        $cache = new ConfigCache($path, $debug);

        return $cache;
    }

    /**
     * @return bool
     */
    public function isInBuild(): bool
    {
        return $this->inBuild;
    }

    /**
     * @param bool $inBuild
     */
    public function setInBuild(bool $inBuild): void
    {
        $this->inBuild = $inBuild;
    }
}

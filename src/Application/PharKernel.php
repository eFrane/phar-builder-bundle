<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\Bundle\DependencyInjection\Compiler\HideDefaultConsoleCommandsFromPharPass;
use EFrane\PharBuilder\Command\PharCommandInterface;
use EFrane\PharBuilder\Exception\PharApplicationException;
use function is_dir;
use function mkdir;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use function sys_get_temp_dir;

class PharKernel extends Kernel implements PharKernelInterface
{
    use MicroKernelTrait;

    public const PHAR_CONTAINER_CACHE_DIR = 'phar_container/';

    /**
     * @var string Path to the ConfigCache of the compiled container
     */
    private $containerPath;

    /**
     * @var bool Is the Phar currently being built
     */
    private $inBuild = false;

    public function __construct(string $containerPath, string $environment, bool $debug)
    {
        parent::__construct($environment, $debug);

        $this->containerPath = $containerPath;
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->withPath($this->getProjectDir())->import('config/{packages}/*.yaml');
        $container->withPath($this->getProjectDir())->import('config/{packages}/'.$this->environment.'/*.yaml');

        if (is_file($this->getProjectDir().'/config/services.yaml')) {
            $container->withPath($this->getProjectDir())->import('config/{services}.yaml');
            $container->withPath($this->getProjectDir())->import('config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = \dirname(__DIR__).'/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }

        if (is_file(\dirname(__DIR__).'/../config/services.yaml')) {
            $container->import('/../config/{services}.yaml');
            $container->import('/../config/{services}_'.$this->environment.'.yaml');
        } elseif (is_file($path = \dirname(__DIR__).'/config/services.php')) {
            (require $path)($container->withPath($path), $this);
        }
    }

    public function configureRoutes(RoutingConfigurator $routes): void
    {
        // Do nothing, there are no routes
    }

    protected function build(ContainerBuilder $containerBuilder): void
    {
        $containerBuilder->registerForAutoconfiguration(PharCommandInterface::class)
            ->addTag('phar.command');

        if (!$this->isDebug()) {
            $containerBuilder->addCompilerPass(new HideDefaultConsoleCommandsFromPharPass());
        }
    }

    public function getCacheDir(): string
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

    public function getLogDir(): string
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

    protected function initializeContainer(): void
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
            /* @noinspection PhpIncludeInspection */
            if (!is_file($cachePath) || !is_object($this->container = include $cachePath)) {
                throw PharApplicationException::failedLoadingContainer($cachePath);
            }

            $this->container->set('kernel', $this);

            error_reporting($errorLevel);

            return;
        } catch (\Throwable $e) {
        }
    }

    public function getConfigCache(bool $debug): ConfigCache
    {
        $path = Util::pharRoot().DIRECTORY_SEPARATOR.$this->containerPath;

        if ($this->isInBuild()) {
            // reset pre-built container during build
            $fs = new Filesystem();
            $files = glob($path);

            if (false !== $files) {
                $fs->remove($files);
            }

            $fs->mkdir($path);
        }

        return new ConfigCache($path, $debug);
    }

    public function isInBuild(): bool
    {
        return $this->inBuild;
    }

    public function setInBuild(bool $inBuild): void
    {
        $this->inBuild = $inBuild;
    }
}

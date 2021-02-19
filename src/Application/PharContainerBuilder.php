<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use EFrane\PharBuilder\Bundle\DependencyInjection\Compiler\HideDefaultConsoleCommandsFromPharPass;
use EFrane\PharBuilder\Bundle\DependencyInjection\MultiDumper;
use EFrane\PharBuilder\Config\Config;
use EFrane\PharBuilder\Exception\PharBuildException;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\GraphvizDumper;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Dumper\YamlDumper;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\DependencyInjection\AddAnnotatedClassesToCachePass;
use Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass;
use Symfony\Component\HttpKernel\Kernel;

class PharContainerBuilder
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var PharKernel
     */
    private $kernel;
    /**
     * @var bool
     */
    private $debug;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function build(): void
    {
        if (Util::inPhar()) {
            throw PharBuildException::runningPhar();
        }

        $kernelClass = $this->config->getPharKernel();
        /** @var PharKernelInterface $kernel */
        $kernel = new $kernelClass($this->config->build()->getEnvironment(), $this->config->build()->isDebug());
        $kernel->setInBuild(true);
        $kernel->boot();

        $containerBuilder = $this->buildContainer($kernel);

        $this->dumpContainer($containerBuilder, $kernel->getConfigCache($this->config->build()->isDebug()));
    }

    private function buildContainer(PharKernelInterface $kernel): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addObjectResource($kernel);

        // I know, dirty, some kernel methods really should be public methinks
        $kernelReflection = new \ReflectionClass($kernel);

        $parametersMethod = $kernelReflection->getMethod('getKernelParameters');
        $parametersMethod->setAccessible(true);
        $kernelParameters = $parametersMethod->invoke($kernel);

        // Inside the phar, the project dir is the root dir of the phar, which, in relative terms,
        // is the current directory, since the bootstrap script determines the root
        $kernelParameters['kernel.project_dir'] = '.';
        $containerBuilder->getParameterBag()->add($kernelParameters);

        foreach ($kernel->getBundles() as $bundle) {
            $extension = $bundle->getContainerExtension();

            if ($extension instanceof Extension) {
                $containerBuilder->registerExtension($extension);
            }

            if ($this->debug) {
                $containerBuilder->addObjectResource($bundle);
            }

            $bundle->build($containerBuilder);
        }

        $extensions = [];
        foreach ($containerBuilder->getExtensions() as $extension) {
            $extensions[] = $extension->getAlias();
        }

        $containerBuilder->getCompilerPassConfig()
            ->setMergePass(new MergeExtensionConfigurationPass($extensions));

        $containerLoaderMethod = $kernelReflection->getMethod('getContainerLoader');
        $containerLoaderMethod->setAccessible(true);
        $containerLoader = $containerLoaderMethod->invoke($kernel, $containerBuilder);

        $kernel->registerContainerConfiguration($containerLoader);

        $containerBuilder->addCompilerPass(
            new HideDefaultConsoleCommandsFromPharPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION
        );

        /* @var Kernel $kernel */
        $containerBuilder->addCompilerPass(new AddAnnotatedClassesToCachePass($kernel));

        $containerBuilder->compile(true);

        return $containerBuilder;
    }

    private function dumpContainer(ContainerBuilder $containerBuilder, ConfigCache $cache): void
    {
        $dumper = new MultiDumper($containerBuilder);

        $dumper
            ->add(PhpDumper::class, [
                'as_files' => true,
                'debug'    => $this->debug,
            ]);

        if ($this->config->build()->dumpContainerDebugInfo()) {
            $dumper
                ->add(GraphvizDumper::class)
                ->add(YamlDumper::class);
        }

        $compiledContainer = $dumper->dump();

        $fs = new Filesystem();

        /** @var array<string,string> $phpDump */
        $phpDump = $compiledContainer[PhpDumper::class];
        foreach ($phpDump as $filename => $content) {
            $fs->dumpFile($cache->getPath().$filename, $content);
        }

        if ($this->config->build()->dumpContainerDebugInfo()) {
            /** @var string $graphVizDump */
            $graphVizDump = $compiledContainer[GraphvizDumper::class];
            /** @var string $yamlDump */
            $yamlDump = $compiledContainer[YamlDumper::class];

            $fs->dumpFile($cache->getPath().'container.dot', $graphVizDump);
            $fs->dumpFile($cache->getPath().'container.yml', $yamlDump);
        }
    }
}

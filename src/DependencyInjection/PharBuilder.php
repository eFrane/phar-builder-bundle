<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\DependencyInjection;


use EFrane\PharBuilder\Application\PharKernel;
use EFrane\PharBuilder\Application\Util;
use EFrane\PharBuilder\CompilerPass\HideDefaultConsoleCommandsFromPharPass;
use EFrane\PharBuilder\Exception\PharBuildException;
use RuntimeException;
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
use Symfony\Component\HttpKernel\KernelInterface;

class PharBuilder
{
    /**
     * @var PharKernel
     */
    private $kernel;
    /**
     * @var bool
     */
    private $debug;

    public function build(string $kernelClass, string $environment, bool $debug): void
    {
        if (Util::inPhar()) {
            throw PharBuildException::runningPhar();
        }

        /** @var KernelInterface|PharKernel $kernel */
        $kernel = new $kernelClass($environment, $debug);
        $kernel->setInBuild(true);
        $kernel->boot();

        $containerBuilder = $this->buildContainer();

        $this->dumpContainer($containerBuilder, $this->kernel->getConfigCache($this->debug));
    }

    private function buildContainer(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();

        $containerBuilder->addObjectResource($this->kernel);

        $kernelParameters = $this->kernel->getKernelParameters();

        $kernelParameters['kernel.projectDir'] = '.';
        $containerBuilder->getParameterBag()->add($kernelParameters);

        foreach ($this->kernel->getBundles() as $bundle) {
            $extension = $bundle->getContainerExtension();
            if ($extension instanceof Extension) {
                $containerBuilder->registerExtension($extension);
            }

            $bundle->build($containerBuilder);
        }

        $extensions = [];
        foreach ($containerBuilder->getExtensions() as $extension) {
            $extensions[] = $extension->getAlias();
        }

        $containerBuilder->getCompilerPassConfig()
            ->setMergePass(new MergeExtensionConfigurationPass($extensions));

        $this->kernel->registerContainerConfiguration($this->kernel->getContainerLoader($containerBuilder));

        $containerBuilder->addCompilerPass(
            new HideDefaultConsoleCommandsFromPharPass(),
            PassConfig::TYPE_BEFORE_OPTIMIZATION
        );
        $containerBuilder->addCompilerPass(new AddAnnotatedClassesToCachePass($this->kernel));

        $containerBuilder->compile(true);

        return $containerBuilder;
    }

    private function dumpContainer(ContainerBuilder $containerBuilder, ConfigCache $cache): void
    {
        $dumper = new MultiDumper($containerBuilder);

        $dumper
            ->add(PhpDumper::class, [
                'as_files' => true,
                'debug' => $this->debug
            ])
            ->add(GraphvizDumper::class)
            ->add(YamlDumper::class);

        $compiledContainer = $dumper->dump();

        $fs = new Filesystem();

        foreach ($compiledContainer[PhpDumper::class] as $filename => $content) {
            $fs->dumpFile($cache->getPath() . $filename, $content);
        }

        $fs->dumpFile($cache->getPath() . 'container.dot', $compiledContainer[GraphvizDumper::class]);
        $fs->dumpFile($cache->getPath() . 'container.yml', $compiledContainer[YamlDumper::class]);
    }
}

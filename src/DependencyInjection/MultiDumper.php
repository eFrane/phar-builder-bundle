<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\DependencyInjection;


use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\DumperInterface;

class MultiDumper implements DumperInterface
{
    /**
     * @var array<int,array<string,mixed>>|int[]
     */
    private $dumpers;
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * MultiDumper constructor.
     * @param ContainerBuilder $containerBuilder
     */
    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
        $this->dumpers = [];
    }

    public function add(string $dumper, array $options = []): self
    {
        $this->dumpers[] = compact('dumper', 'options');

        return $this;
    }

    /**
     * @param array $options
     * @return array
     */
    public function dump(array $options = []): array
    {
        $result = [];

        array_map(function (array $dumper) {
            $dumperInstance = new $dumper['dumper']($this->containerBuilder);
            $result[$dumper['dumper']] = $dumperInstance->dump($dumper['options']);
        }, $this->dumpers);

        return $result;
    }
}

<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Bundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\DumperInterface;

class MultiDumper implements DumperInterface
{
    /**
     * @var array<int,array<string,mixed>>
     */
    private $dumpers;
    /**
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * MultiDumper constructor.
     */
    public function __construct(ContainerBuilder $containerBuilder)
    {
        $this->containerBuilder = $containerBuilder;
        $this->dumpers = [];
    }

    /**
     * @param array<string,mixed> $options
     *
     * @return $this
     */
    public function add(string $dumper, array $options = []): self
    {
        $this->dumpers[] = compact('dumper', 'options');

        return $this;
    }

    /**
     * @param array<string,mixed> $options
     *
     * @return array<string,string|mixed>
     */
    public function dump(array $options = []): array
    {
        $result = [];

        foreach ($this->dumpers as $dumper) {
            /** @var string $dumperClass */
            $dumperClass = $dumper['dumper'];

            $dumperInstance = new $dumperClass($this->containerBuilder);
            $result[$dumperClass] = $dumperInstance->dump($dumper['options']);
        }

        return $result;
    }
}

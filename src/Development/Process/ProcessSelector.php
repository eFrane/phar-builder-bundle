<?php

declare(strict_types=1);

namespace EFrane\PharBuilder\Development\Process;

use EFrane\PharBuilder\Exception\PharBuildException;

final class ProcessSelector
{
    /**
     * @var array<string,IdentifiableProcessProviderInterface>
     */
    private $providers;

    /**
     * ProcessSelector constructor.
     *
     * @param iterable|IdentifiableProcessProviderInterface[] $providers
     */
    public function __construct(iterable $providers)
    {
        $this->providers = [];
        foreach ($providers as $provider) {
            $this->providers[$provider->getName()] = $provider;
        }
    }

    public function get(string $name): IdentifiableProcessProviderInterface
    {
        if (!array_key_exists($name, $this->providers)) {
            throw PharBuildException::unknownToolProcessName($name);
        }

        return $this->providers[$name];
    }
}

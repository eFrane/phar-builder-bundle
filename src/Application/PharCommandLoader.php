<?php


namespace EFrane\PharBuilder\Application;


use EFrane\PharBuilder\Command\DebugCommand;
use EFrane\PharBuilder\Command\PharCommand;
use EFrane\PharBuilder\Development\Config\Config;

/**
 * Class CommandLoader
 * @package EFrane\PharBuilder\Util
 */
class PharCommandLoader
{
    /**
     * @var array|PharCommand[]
     */
    private $commands;

    public function __construct(Config $config, iterable $commands)
    {
        $this->commands = [];

        foreach ($commands as $command) {
            if (!$config->build()->includeDebugCommands() && $command instanceof DebugCommand) {
                // Skip debug commands
                continue;
            }

            $this->commands[] = $command;
        }
    }

    /**
     * @return array|PharCommand[]
     */
    public function getCommands(): array
    {
        return $this->commands;
    }
}

<?php


namespace EFrane\PharBuilder\Application;


/**
 * Class CommandLoader
 * @package EFrane\PharBuilder\Util
 */
class PharCommandLoader
{
    /**
     * @var iterable
     */
    private $commands;

    public function __construct(iterable $commands)
    {
        $this->commands = $commands;
    }

    public function getCommands(): iterable
    {
        return $this->commands;
    }
}

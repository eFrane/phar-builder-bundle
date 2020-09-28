<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Command;


/**
 * Interface DebugCommand
 *
 * Commands which just exist for debugging purposes can be excluded from the phar build
 * with the config setting build.includeDebugCommands.
 *
 * This interface helps identifying these commands in the loader and requires no methods for this task.
 *
 * @package EFrane\PharBuilder\Command
 */
interface DebugCommand
{
}

<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Command;

use Symfony\Component\Console\Command\Command;

abstract class PharCommand extends Command implements PharCommandInterface
{
}

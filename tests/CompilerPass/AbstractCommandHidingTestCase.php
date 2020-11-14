<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Tests\CompilerPass;

use EFrane\PharBuilder\Command\PharCommand;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Definition;

abstract class AbstractCommandHidingTestCase extends AbstractCompilerPassTestCase
{
    protected function getDefaultCommandDefinition(): Definition
    {
        $testCommand = new class() extends Command {
            protected static $defaultName = 'default:command:for:testing';
        };

        $testClassName = get_class($testCommand);
        $definition = new Definition($testClassName);
        $definition->addTag('console.command');

        return $definition;
    }

    protected function getPharCommandDefinition(): Definition
    {
        $testCommand = new class() extends PharCommand {
            protected static $defaultName = 'phar:command:for:testing';
        };

        $testClassName = get_class($testCommand);
        $definition = new Definition($testClassName);

        $definition->addTag('console.command');
        $definition->addTag('phar.command');

        return $definition;
    }
}

<?php

declare(strict_types=1);

namespace EFrane\PharBuilder\Application;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputOption;

/**
 * Application class supporting running in phar and outside.
 */
class PharApplication extends Application
{
    /**
     * @var bool
     */
    private $commandsRegistered = false;

    public function __construct(PharKernel $kernel)
    {
        parent::__construct($kernel);

        $this->setDefaultCommand('list');

        // reset input definition to remove framework bundle extras
        $inputDefinition = $this->getDefaultInputDefinition();

        $inputDefinition->addOption(
            new InputOption(
                '--cwd',
                '-C',
                InputOption::VALUE_REQUIRED,
                'Change the working directory'
            )
        );

        $this->setDefinition($inputDefinition);
    }

    public function registerCommands(): void
    {
        // This is just to avoid registering commands while registering commands (register may be called multiple times)
        if ($this->commandsRegistered) {
            return;
        }

        $this->commandsRegistered = true;

        $this->getKernel()->boot();

        $container = $this->getKernel()->getContainer();

        /** @var PharCommandLoader $commandLoader */
        $commandLoader = $container->get(PharCommandLoader::class);

        $this->addCommands($commandLoader->getCommands());
    }
}

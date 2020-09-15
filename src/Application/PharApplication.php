<?php


namespace EFrane\PharBuilder\Application;


use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputOption;

/**
 * Application class supporting running in phar and outside
 *
 * @package App
 */
class PharApplication extends Application
{
    private $commandsRegistered = false;

    public function __construct(Kernel $kernel)
    {
        parent::__construct($kernel);

        // change name and version from framework bundle settings
        $this->setName('test');
        $this->setVersion('@git-tag@');
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
        if ($this->commandsRegistered) {
            return;
        }

        $this->commandsRegistered = true;

        $this->getKernel()->boot();

        $container = $this->getKernel()->getContainer();

        $commandLoader = $container->get(PharCommandLoader::class);

        if (isset($commandLoader)) {
            foreach ($commandLoader->getCommands() as $command) {
                $this->add($command);
            }
        }
    }

    public function getLongVersion(): string
    {
        return \sprintf('%s @ %s', $this->getName(), $this->getVersion());
    }
}

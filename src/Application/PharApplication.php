<?php


namespace EFrane\PharBuilder\Application;


use EFrane\PharBuilder\Exception\PharApplicationException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use function sprintf;

/**
 * Application class supporting running in phar and outside
 *
 * @package App
 */
class PharApplication extends Application
{
    private $commandsRegistered = false;

    public function __construct(PharKernel $kernel)
    {
        parent::__construct($kernel);

        // change version from framework bundle settings
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
        // This is just to avoid registering commands while registering commands (register may be called multiple times)
        if ($this->commandsRegistered) {
            return;
        }

        $this->commandsRegistered = true;

        $this->getKernel()->boot();

        $container = $this->getKernel()->getContainer();

        /** @var PharCommandLoader $commandLoader */
        $commandLoader = $container->get(PharCommandLoader::class);

        if (!isset($commandLoader)) {
            throw PharApplicationException::missingCommandLoader();
        }

        $this->addCommands($commandLoader->getCommands());
    }

    public function getLongVersion(): string
    {
        return sprintf('%s @ %s', $this->getName(), $this->getVersion());
    }
}

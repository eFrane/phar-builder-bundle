<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Development\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Twig\Environment;

class SetupInfoCommand extends Command
{
    public static $defaultName = 'phar:setup-info';

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(Environment $twig, string $name = null)
    {
        parent::__construct($name);
        $this->twig = $twig;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln($this->twig->render('@PharBuilder/setup-info.txt.twig'));

        return Command::SUCCESS;
    }
}

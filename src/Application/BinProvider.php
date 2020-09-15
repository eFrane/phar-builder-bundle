<?php
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use Exception;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Dotenv\Dotenv;

class BinProvider
{
    /**
     * @var string
     */
    private $kernelClass; // = PharKernel::class;
    /**
     * @var string
     */
    private $applicationClass; // = PharApplication::class;

    public function __construct(string $kernelClass, string $applicationClass)
    {
        $this->kernelClass = $kernelClass;
        $this->applicationClass = $applicationClass;
    }

    public function __invoke(): int
    {
        if (!in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
            echo 'Warning: The console should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
        }

        set_time_limit(0);

        $input = new ArgvInput();

        $workingDirectory = (string)$input->getParameterOption(['--cwd', '-C'], getcwd(), true);

        if (is_dir($workingDirectory)) {
            chdir($workingDirectory);
        } else {
            echo 'Error: Requested working directory '.$workingDirectory.' does not exist'.PHP_EOL;
            return 1;
        }

        putenv('APP_ENV=prod');

        (new Dotenv())->bootEnv(dirname(__DIR__, 2).'/.env');

        $kernel = new $this->kernelClass('prod', false);
        $application = new $this->applicationClass($kernel);

        try {
            return $application->run($input);
        } catch (Exception $e) {
            return 1;
        }
    }
}

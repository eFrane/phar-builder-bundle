<?php

declare(strict_types=1);
/**
 * @copyright 2020
 * @author Stefan "eFrane" Graupner <stefan.graupner@gmail.com>
 */

namespace EFrane\PharBuilder\Application;

use Exception;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Dotenv\Dotenv;

final class BinProvider
{
    /**
     * @var string|class-string|PharKernel
     */
    private $kernelClass;
    /**
     * @var string|class-string|PharApplication
     */
    private $applicationClass;
    /**
     * @var string
     */
    private $containerPath;
    /**
     * @var bool
     */
    private $debug;

    public function __construct(
        string $containerPath,
        string $kernelClass,
        string $applicationClass,
        bool $debug
    ) {
        $this->containerPath = $containerPath;
        $this->kernelClass = $kernelClass;
        $this->applicationClass = $applicationClass;
        $this->debug = $debug;
    }

    public function __invoke(): int
    {
        if (!in_array(PHP_SAPI, ['cli', 'phpdbg', 'embed'], true)) {
            echo 'Warning: The console should be invoked via the CLI version of PHP, not the '.PHP_SAPI.' SAPI'.PHP_EOL;
        }

        set_time_limit(0);

        $workingDirectory = $this->applicationClass::setupWorkingDirectory();

        if (is_dir($workingDirectory)) {
            chdir($workingDirectory);
        } else {
            echo 'Error: Requested working directory '.$workingDirectory.' does not exist'.PHP_EOL;

            return 1;
        }

        putenv('APP_ENV=prod');

        (new Dotenv())->bootEnv(Util::pharRoot().'/.env');

        $kernel = new $this->kernelClass($this->containerPath, 'prod', $this->debug);
        $application = new $this->applicationClass($kernel);

        try {
            return $application->run(new ArgvInput());
        } catch (Exception $e) {
            return 1;
        }
    }
}

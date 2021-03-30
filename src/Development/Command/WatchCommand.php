<?php

namespace EFrane\PharBuilder\Development\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpProcess;

class WatchCommand extends DependenciesUpdatingCommand
{
    protected static $defaultName = 'phar:watch';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $watchScript = $this->getWatchScript();

        $phpProcess = new PhpProcess($watchScript);

        $phpProcess->setTimeout(0);
        $phpProcess->setTty(true);

        $phpProcess->run();

        return Command::SUCCESS;
    }

    private function getWatchScript(): string
    {
        $cwd = getcwd();

        return <<<PHP
<?php

require 'vendor/autoload.php';

use Symfony\Component\Finder\Finder;
use Yosymfony\ResourceWatcher\Crc32ContentHash;
use Yosymfony\ResourceWatcher\ResourceCacheMemory;
use Yosymfony\ResourceWatcher\ResourceWatcher;

\$finder = new Finder();
\$finder->files()
    ->name('*.php')
    ->in('$cwd');

\$hashContent = new Crc32ContentHash();
\$resourceCache = new ResourceCacheMemory();
\$watcher = new ResourceWatcher(\$resourceCache, \$finder, \$hashContent);
\$watcher->initialize();

while (true) {
    \$changeset = \$watcher->findChanges();
     
    if (\$changeset->hasChanges()) {
        \$process = new \Symfony\Component\Process\Process([
            'php',
            'bin/console',
            'phar:build',
            '--no-update-dependencies'
        ], '$cwd');
        
        \$process->setTty(true);
        \$process->run();
    }
    
    usleep(10000);
}
PHP;
    }
}

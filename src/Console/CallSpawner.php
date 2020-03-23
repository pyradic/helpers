<?php

namespace Pyro\Helpers\Console;

use Composer\XdebugHandler\XdebugHandler;
use Symfony\Component\Process\Process;

/**
 * @mixin \Illuminate\Console\Command
 */
trait CallSpawner
{

    protected function spawnCall($args)
    {
        $phpBin = $_SERVER[ '_' ];
        $out    = $this->getOutput();

        $verbosity = $out->isVerbose() ? '-v' : '';
        $verbosity = $out->isVeryVerbose() ? '-vv' : $verbosity;
        $verbosity = $out->isDebug() ? '-vvv' : $verbosity;
        $process   = new Process("{$phpBin} artisan {$verbosity} {$args}");
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo $buffer;
            } else {
                echo $buffer;
            }
        });
    }
}

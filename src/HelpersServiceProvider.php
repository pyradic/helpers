<?php

namespace Pyro\Helpers;

use Illuminate\Support\ServiceProvider;
use Pyro\Helpers\Console\AddonDisableCommand;
use Pyro\Helpers\Console\AddonEnableCommand;
use Pyro\Helpers\Console\AddonListCommand;
use Pyro\Helpers\Console\MakeStreamEvents;
use Pyro\Helpers\Console\UninstallCommand;

class HelpersServiceProvider extends ServiceProvider
{
    protected $commands = [
        'command.addon.list'         => AddonListCommand::class,
        'command.addon.disable'      => AddonDisableCommand::class,
        'command.addon.enable'       => AddonEnableCommand::class,
        'command.uninstall'          => UninstallCommand::class,
        'command.make.stream.events' => MakeStreamEvents::class,
    ];

    public function register()
    {
        foreach ($this->commands as $key => $class) {
            $this->app->singleton($key, function ($app) use ($class) {
                return $app->build($class);
            });
        }
        $this->commands(array_keys($this->commands));
    }
}

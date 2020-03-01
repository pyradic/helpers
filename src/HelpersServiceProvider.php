<?php

namespace Pyro\Helpers;

use Illuminate\Support\ServiceProvider;
use Pyro\Helpers\Console\AddonDisableCommand;
use Pyro\Helpers\Console\AddonEnableCommand;
use Pyro\Helpers\Console\AddonListCommand;
use Pyro\Helpers\Console\UninstallCommand;

class HelpersServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('command.addon.list', function ($app) {
            return new AddonListCommand();
        });
        $this->app->singleton('command.addon.disable', function ($app) {
            return new AddonDisableCommand();
        });
        $this->app->singleton('command.addon.enable', function ($app) {
            return new AddonEnableCommand();
        });
        $this->app->singleton('command.uninstall', function ($app) {
            return new UninstallCommand();
        });
        $this->commands([
            'command.addon.list',
            'command.addon.disable',
            'command.addon.enable',
            'command.uninstall',
        ]);
    }
}

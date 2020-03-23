<?php

namespace Pyro\Helpers\Console;

use Composer\XdebugHandler\XdebugHandler;
use Illuminate\Contracts\Foundation\Application;

class LoadXdebugHandler
{
    /** @var XdebugHandler */
    protected $handler;
    public function bootstrap(Application $app)
    {
        $this->handler = new XdebugHandler('artisan');
        $this->handler->check();
    }
}

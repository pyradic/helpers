<?php

namespace Pyro\Helpers\Console;

use Illuminate\Console\Command;
use Pyro\Helpers\Generator\StreamEventGenerator;

class MakeStreamEvents extends Command
{
    protected $signature = 'make:stream:events
                                               {addon : The addon containing the stream}
                                               {stream : The stream slug}
                                               {events=deleted,created,updated,saved : Observable model events to create classes for }
                                               {--observer : (Over)write the Observer class with the event dispatchers }';

    protected $description = 'Scaffold events for addon stream';

    public function handle()
    {
        dispatch_now(
            new StreamEventGenerator([
                'addon'          => $this->argument('addon'),
                'stream'         => $this->argument('stream'),
                'events'         => explode(',', $this->argument('events')),
                'createObserver' => $this->option('observer'),
            ])
        );
        $this->info('Stream events generated');
    }
}

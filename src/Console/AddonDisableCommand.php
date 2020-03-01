<?php

namespace Pyro\Helpers\Console;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Extension\Command\DisableExtension;
use Anomaly\Streams\Platform\Addon\Module\Command\DisableModule;
use Illuminate\Console\Command;

class AddonDisableCommand extends Command
{
    protected $signature = 'addon:disable {addon?}';

    protected $description = 'Disable an addon';

    public function handle(AddonCollection $addons)
    {
        $enabled = $addons->installable()->enabled();
        $slug    = $this->argument('addon') ?? $this->choice('Disable addon', $enabled->pluck('slug')->toArray());
        $addon   = $enabled->findBySlug($slug);

        if ( ! $addon) {
            return $this->error('Not a valid addon');
        }

        if ($addon->getType() === 'extension') {
            dispatch_now(new DisableExtension($addon));
        } elseif ($addon->getType() === 'module') {
            dispatch_now(new DisableModule($addon));
        } else {
            return $this->error('Not a valid addon type');
        }
        $this->info("Addon [{$addon->getSlug()}] disabled");
    }
}

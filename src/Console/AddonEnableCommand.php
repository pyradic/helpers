<?php

namespace Pyro\Helpers\Console;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Extension\Command\DisableExtension;
use Anomaly\Streams\Platform\Addon\Extension\Command\EnableExtension;
use Anomaly\Streams\Platform\Addon\Module\Command\DisableModule;
use Anomaly\Streams\Platform\Addon\Module\Command\EnableModule;
use Illuminate\Console\Command;

class AddonEnableCommand extends Command
{
    protected $signature = 'addon:enable {addon?}';

    protected $description = 'Enable an addon';

    public function handle(AddonCollection $addons)
    {
        $disabled = $addons->installable()->disabled();
        $slug    = $this->argument('addon') ?? $this->choice('Disable addon', $disabled->pluck('slug')->toArray());
        $addon   = $disabled->findBySlug($slug);

        if ( ! $addon) {
            return $this->error('Not a valid addon');
        }

        if ($addon->getType() === 'extension') {
            dispatch_now(new EnableExtension($addon));
        } elseif ($addon->getType() === 'module') {
            dispatch_now(new EnableModule($addon));
        } else {
            return $this->error('Not a valid addon type');
        }
        $this->info("Addon [{$addon->getSlug()}] enabled");
    }
}

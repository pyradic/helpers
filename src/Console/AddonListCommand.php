<?php

namespace Pyro\Helpers\Console;

use Anomaly\Streams\Platform\Addon\Addon;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Extension\ExtensionCollection;
use Anomaly\Streams\Platform\Addon\FieldType\FieldTypeCollection;
use Anomaly\Streams\Platform\Addon\Module\ModuleCollection;
use Anomaly\Streams\Platform\Addon\Theme\ThemeCollection;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AddonListCommand extends Command
{
    protected $signature = 'addon:list
                                      {search?}
                                      {--i|installed} {--u|uninstalled}
                                      {--E|enabled} {--D|disabled}
                                      {--m|modules} {--e|extensions} {--f|fields}  {--t|themes}';

    protected $description = 'List addons';

    public function handle(AddonCollection $addons)
    {
        if ($this->option('modules')) {
            $this->listModules($addons->modules);
        }
        if ($this->option('extensions')) {
            $this->listExtensions($addons->extensions);
        }
        if ($this->option('fields')) {
            $this->listFields($addons->field_types);
        }
        if ($this->option('themes')) {
            $this->listThemes($addons->themes);
        }

        if (
            ! $this->option('modules') &&
            ! $this->option('extensions') &&
            ! $this->option('fields') &&
            ! $this->option('themes')
        ) {
            $this->listModules($addons->modules);
            $this->listExtensions($addons->extensions);
            $this->listFields($addons->field_types);
            $this->listThemes($addons->themes);
        }
    }

    protected function listThemes(ThemeCollection $themes)
    {
        $this->getOutput()->title('Themes');
        $rows = [];
        foreach ($this->filterSearch($themes) as $theme) {
            $rows[] = [
                trans($theme->getNamespace('addon.name')),
                $theme->getNamespace(),
            ];
        }
        $this->table([ 'namespace' ], $rows);
    }

    protected function listFields(FieldTypeCollection $fields)
    {
        $this->getOutput()->title('Fields');
        $rows = [];
        foreach ($this->filterSearch($fields) as $field) {
            $rows[] = [
                trans($field->getNamespace('addon.name')),
                $field->getNamespace(),
            ];
        }
        $this->table([ 'namespace' ], $rows);
    }

    protected function listExtensions(ExtensionCollection $extensions)
    {
        $this->getOutput()->title('Extensions');
        foreach ([ 'installed', 'uninstalled', 'enabled', 'disabled' ] as $state) {
            if ($this->option($state)) {
                $extensions = $extensions->{$state}();
            }
        }
        $rows = [];
        foreach ($this->filterSearch($extensions) as $extension) {
            $rows[] = [
                trans($extension->getNamespace('addon.name')),
                $extension->isInstalled() ? "<fg=green>{$extension->getNamespace()}</>" : "<fg=red>{$extension->getNamespace()}</>",
                ($extension->isEnabled() ? '<fg=green>yes</>' : '<fg=yellow>-</>'),
            ];
        }
        $this->table([ 'namespace', 'enabled' ], $rows);
    }

    protected function listModules(ModuleCollection $modules)
    {
        $this->getOutput()->title('Modules');
        foreach ([ 'installed', 'uninstalled', 'enabled', 'disabled' ] as $state) {
            if ($this->option($state)) {
                $modules = $modules->{$state}();
            }
        }
        $rows = [];
        foreach ($this->filterSearch($modules) as $module) {
            $rows[] = [
                trans($module->getNamespace('addon.name')),
                $module->isInstalled() ? "<fg=green>{$module->getNamespace()}</>" : "<fg=red>{$module->getNamespace()}</>",
                ($module->isEnabled() ? '<fg=green>yes</>' : '<fg=yellow>-</>'),
            ];
        }
        $this->table([ 'namespace', 'enabled' ], $rows);
    }

    /**
     * @param \Anomaly\Streams\Platform\Addon\AddonCollection $addons
     *
     * @return \Anomaly\Streams\Platform\Addon\Addon[]|\Anomaly\Streams\Platform\Addon\AddonCollection
     */
    protected function filterSearch($addons)
    {
        if($search = $this->argument('search')) {
            return $addons->filter(function (Addon $addon) use ($search) {
                return Str::is("*{$search}*", $addon->getNamespace());
            });
        }
        return $addons;
    }
}

<?php

namespace Pyro\Helpers\Console;

use DB;
use Illuminate\Console\Command;

class UninstallCommand extends Command
{
    protected $signature = 'uninstall';

    protected $description = 'Uninstall PyroCMS';

    public function handle()
    {
        $this->call('env:set', [ 'line' => 'INSTALLED=false' ]);
        $schema = DB::getDoctrineSchemaManager();
        $tables = $schema->listTables();

        if ($this->confirm('This will drop all tables', true)) {
            foreach ($tables as $table) {
                $this->info(" - Dropping table: <comment>{$table->getName()}</comment>");
                $schema->dropTable($table->getName());
            }
        }
        $this->info('Database truncated');
    }
}

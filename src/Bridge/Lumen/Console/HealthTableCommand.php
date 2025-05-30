<?php

namespace Health\Bridge\Lumen\Console;

use Illuminate\Console\MigrationGeneratorCommand;

class HealthTableCommand extends MigrationGeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'health:table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a migration for the health database table';

    /**
     * Get the migration table name.
     *
     * @return string
     */
    protected function migrationTableName()
    {
        $table = config('health.log.table', 'health_call_logs');
        return $table;
    }

    /**
     * Get the path to the migration stub file.
     *
     * @return string
     */
    protected function migrationStubFile()
    {
        return __DIR__.'/database.stub';
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class CreateTestTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-test-tenant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a test tenant for development and testing purposes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Inserting test tenant...');
        $test = Tenant::create('test', 'Test Music Club Pty Ltd', 'Australia/Perth', ['has_gratis' => true]);

        $this->info('Creating domain...');
        $test->domains()->create(['domain' => 'test']);

        $this->info('Adding ensembles...');
        $test->ensembles()->create(['name' => 'The Test Tones']);
        $test->ensembles()->create(['name' => 'Test Tones Youth Chorus']);

        $this->info('Test tenant created.');

        return Command::SUCCESS;
    }
}

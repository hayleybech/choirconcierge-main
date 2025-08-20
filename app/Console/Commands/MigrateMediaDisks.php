<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class MigrateMediaDisks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:migrate-disks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Replace obsolete disk names with their replacements';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('media')
            ->where('disk', 'global-public')
            ->update(['disk' => 'media']);

        DB::table('media')
            ->where('conversions_disk', 'global-public')
            ->update(['conversions_disk' => 'media']);

        DB::table('media')
            ->where('disk', 'public')
            ->update(['disk' => 'media']);

        DB::table('media')
            ->where('conversions_disk', 'public')
            ->update(['conversions_disk' => 'media']);

        return CommandAlias::SUCCESS;
    }
}

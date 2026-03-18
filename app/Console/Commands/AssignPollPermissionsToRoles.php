<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AssignPollPermissionsToRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:assign-poll-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assigns poll permissions to roles based on their current abilities.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting poll permission assignment...');

        $totalRoles = \Illuminate\Support\Facades\DB::table('roles')->count();
        $bar = $this->output->createProgressBar($totalRoles);

        \Illuminate\Support\Facades\DB::table('roles')
            ->select(['id', 'name', 'abilities'])
            ->chunkById(100, function (\Illuminate\Support\Collection $roles) use ($bar) {
                foreach ($roles as $role) {
                    $abilities = json_decode($role->abilities, true) ?? [];

                    $newAbilities = ['polls_view'];

                    if ($role->name !== 'User') {
                        $newAbilities = array_merge($newAbilities, [
                            'polls_create',
                            'polls_update',
                            'polls_delete',
                        ]);
                    }

                    $updatedAbilities = array_unique(array_merge($abilities, $newAbilities));

                    \Illuminate\Support\Facades\DB::table('roles')
                        ->where('id', $role->id)
                        ->update(['abilities' => json_encode(array_values($updatedAbilities))]);

                    $bar->advance();
                }
            });

        $bar->finish();
        $this->newLine();
        $this->info('Poll permissions successfully assigned.');

        return Command::SUCCESS;
    }
}

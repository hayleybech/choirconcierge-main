<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(CriticalDataSeeder::class);
        $this->command->info('All critical data seeded!');

        if (App::environment('local')) {
            $this->call(DummyDataSeeder::class);
            $this->command->info('All dummy data seeded!');
        }
    }
}

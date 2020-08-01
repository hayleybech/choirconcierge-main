<?php

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

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

        if(App::environment('local')){
            $this->call(DummyDataSeeder::class);
            $this->command->info('All dummy data seeded!');
        }
    }
}

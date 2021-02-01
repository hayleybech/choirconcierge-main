<?php

namespace Database\Seeders;

use App\Models\Role;
use Database\Seeders\Critical;
use Database\Seeders\Critical\CriticalEventSeeder;
use Database\Seeders\Critical\CriticalSongSeeder;
use Database\Seeders\Critical\CriticalUserSeeder;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

class CriticalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(CriticalUserSeeder::class);
		$this->command->info('Critical User data seeded!');

		$this->call(CriticalSongSeeder::class);
		$this->command->info('Critical Song data seeded!');

        $this->call(CriticalEventSeeder::class);
        $this->command->info('Critical Event data seeded!');
    }
}

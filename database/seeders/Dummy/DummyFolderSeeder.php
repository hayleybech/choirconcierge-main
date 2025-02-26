<?php

namespace Database\Seeders\Dummy;

use App\Models\Folder;
use Illuminate\Database\Seeder;

class DummyFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Folder::factory()
            ->count(10)
            ->create();
    }
}

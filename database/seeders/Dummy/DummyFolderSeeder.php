<?php

namespace Database\Seeders\Dummy;

use Illuminate\Database\Seeder;
use App\Models\Folder;

class DummyFolderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Folder::factory()->count(10)->create();
    }
}

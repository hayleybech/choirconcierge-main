<?php

namespace Database\Seeders\Dummy;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DummyDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Document::factory()
            ->count(30)
            ->create();
    }
}

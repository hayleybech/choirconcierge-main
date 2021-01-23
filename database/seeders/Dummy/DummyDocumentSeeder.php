<?php

namespace Database\Seeders\Dummy;

use Illuminate\Database\Seeder;
use App\Models\Document;

class DummyDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Document::factory()->count(30)->create();
    }
}

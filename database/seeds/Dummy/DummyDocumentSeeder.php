<?php

use Illuminate\Database\Seeder;

class DummyDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        factory(App\Models\Document::class, 30)->create();
    }
}

<?php

namespace Database\Seeders\Dummy;

use App\Models\ForumCategory;
use Illuminate\Database\Seeder;

class DummyForumCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect(['Rehearsal & Performance', 'Music & Repertoire', 'Choir Community'])
            ->each(fn (string $title) => ForumCategory::query()->firstOrCreate(
                ['title' => $title],
                ['is_choir_wide' => true],
            ));
    }
}

<?php

use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->deleteFiles();

        DB::table('documents')->delete();

        factory(App\Models\Document::class, 30)->create();
    }

    private function deleteFiles(): void
    {
        $filesystem = new Illuminate\Filesystem\Filesystem();
        $filesystem->cleanDirectory('storage/app/documents');
    }
}

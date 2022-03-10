<?php

namespace Database\Factories;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentFactory extends Factory
{
    /** @var string */
    protected $model = Document::class;

    public function definition(): array
    {
        $folders = Folder::all();

        $name = $this->faker->word().'.'.$this->faker->fileExtension();
        $file = UploadedFile::fake()->create($name, 5);
        //$filepath = Storage::disk('public')->putFile( Document::getDownloadsPath(), $file);

        return [
            'title' => $this->faker->sentence(3, true),
            'folder_id' => $folders->random(1)->first()->id,
            //'filepath'          => $name,
            'document_upload' => $file,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

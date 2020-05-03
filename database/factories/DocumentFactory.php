<?php

use App\Models\Document;
use App\Models\Folder;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Http\UploadedFile;

/** @var Factory $factory */

$folders = Folder::all();

/*
 * I'm going to use the actual storage in this case,
 * as I want to be able to create demo sites with file downloads.
 *
 * @todo Create a separate Document factory/state using fake storage.
 */
$factory->define(Document::class, function (Faker $faker) use($folders) {
    $name = $faker->word().'.'.$faker->fileExtension;
    $file = UploadedFile::fake()->create($name, 5);
    $filepath = Storage::disk('public')->putFile( Document::getDownloadsPath(), $file);

    return [
        'title'     => $faker->sentence(3, true),
        'folder_id' => $folders->random(1)->first()->id,
        'filepath'  => $name,
    ];
});

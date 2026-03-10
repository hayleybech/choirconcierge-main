<?php

use App\Imports\ChoirConciergeSingersImport;
use App\Models\Ensemble;
use App\Models\Enrolment;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;


beforeEach(function () {
    // Seed standard voice parts
    VoicePart::factory()->create(['title' => 'Soprano']);
    VoicePart::factory()->create(['title' => 'Alto']);
    VoicePart::factory()->create(['title' => 'Tenor']);
    VoicePart::factory()->create(['title' => 'Bass']);

    // Seed singer categories
    SingerCategory::factory()->create(['name' => 'Members']);
    SingerCategory::factory()->create(['name' => 'Archived Members']);

    // Seed ensembles
    Ensemble::factory()->create(['name' => 'Ensemble 1']);
    Ensemble::factory()->create(['name' => 'Ensemble 2']);
});

it('imports singers with multiple ensembles and voice parts', function () {
    $csvContent = "email,first_name,last_name,voice_part,bha_id\n" .
                  "test@example.com,John,Doe,\"Ensemble 1 - Alto;Ensemble 2 - Soprano\",12345";
    
    $file = UploadedFile::fake()->createWithContent('import.csv', $csvContent);

    Excel::import(new ChoirConciergeSingersImport, $file);

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull();
    
    $membership = $user->membership;
    expect($membership)->not->toBeNull();

    $enrolments = $membership->enrolments()->with(['ensemble', 'voice_part'])->get();
    expect($enrolments)->toHaveCount(2);

    $enrolment1 = $enrolments->where('ensemble.name', 'Ensemble 1')->first();
    expect($enrolment1)->not->toBeNull();
    expect($enrolment1->voice_part->title)->toBe('Alto');

    $enrolment2 = $enrolments->where('ensemble.name', 'Ensemble 2')->first();
    expect($enrolment2)->not->toBeNull();
    expect($enrolment2->voice_part->title)->toBe('Soprano');
});

it('falls back to default ensemble if only voice part is provided', function () {
    $csvContent = "email,first_name,last_name,voice_part,bha_id\n" .
                  "test2@example.com,Jane,Doe,Tenor,67890";
    
    $file = UploadedFile::fake()->createWithContent('import.csv', $csvContent);

    Excel::import(new ChoirConciergeSingersImport, $file);

    $user = User::where('email', 'test2@example.com')->first();
    $enrolments = $user->membership->enrolments()->with(['ensemble', 'voice_part'])->get();
    
    expect($enrolments)->toHaveCount(1);
    expect($enrolments->first()->voice_part->title)->toBe('Tenor');
    // It should pick the first ensemble by default
    expect($enrolments->first()->ensemble->name)->toBe('Ensemble 1');
});

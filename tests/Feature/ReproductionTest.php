<?php

use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

test('update@ fails when using PATCH with multipart/form-data (simulated)', function () {
    Storage::fake('public');
    
    $user = User::factory()->has(Membership::factory())->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);
    
    actingAs($user);

    $file = UploadedFile::fake()->image('avatar.jpg');

    // This simulates what happens when PHP/Laravel receives a PATCH request with multipart/form-data
    // PHP does not populate $_POST or $_FILES for PATCH requests.
    $response = $this->call('PATCH', the_tenant_route('account.update'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'avatar' => $file,
    ], files: [
        'avatar' => $file
    ]);

    $response->dump();
});

test('update@ works when using POST with _method=PATCH and multipart/form-data', function () {
    Storage::fake('public');
    
    $user = User::factory()->has(Membership::factory())->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);
    
    actingAs($user);

    $file = UploadedFile::fake()->image('avatar.jpg');

    // This is how Inertia should handle PATCH with files (or how we are now forcing it)
    $response = $this->post(the_tenant_route('account.update'), [
        '_method' => 'PATCH',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'avatar' => $file,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    
    $user->refresh();
    expect($user->getMedia('avatar'))->not->toBeEmpty();
});

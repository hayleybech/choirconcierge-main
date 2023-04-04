<?php

use App\Jobs\ResetDemoSite;
use App\Models\Song;
use App\Models\Tenant;
use Database\Seeders\Dummy\DummySongSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\seed;
use function PHPUnit\Framework\assertGreaterThan;
use function PHPUnit\Framework\assertNotEquals;
use function PHPUnit\Framework\assertNotNull;

uses(RefreshDatabase::class);

beforeEach(function() {
    $this->tenancy = false;
});

it('re-creates the demo tenant', function () {
    $demo_old = Tenant::create('demo', 'Hypothetical Harmony', 'Australia/Perth');
    $demo_old_created_at = $demo_old->created_at;

    sleep(1); // less than 1 sec will fail

    $job = new App\Jobs\ResetDemoSite();
    $job->handle();

    assertNotEquals($demo_old_created_at, Tenant::find('demo')->created_at);
});

it('re-creates the tenant data', function () {
    $demo = Tenant::create('demo', 'Hypothetical Harmony', 'Australia/Perth');
    $demo->domains()->create(['domain' => 'demo']);

    assertGreaterThan(0, DB::table('songs')->count());

    $demo_old_song_id = Song::query()->where('tenant_id', $demo->id)->first()->id;

    $job = new ResetDemoSite();
    $job->handle();

    assertDatabaseMissing('songs', ['id' => $demo_old_song_id]);

    assertGreaterThan(0, DB::table('songs')->count());
});

it('does not delete data for other tenants', function () {
    $demo = Tenant::create('demo', 'Hypothetical Harmony', 'Australia/Perth');
    $demo->domains()->create(['domain' => 'demo']);

    $other = Tenant::create('other', 'Save Me', 'Australia/Perth');
    $other->run(function () {
        seed(DummySongSeeder::class);
    });

    assertGreaterThan(0, DB::table('songs')->where('tenant_id', 'other')->count());
    $song_to_not_delete = Song::query()->where('tenant_id', 'other')->first();

    $job = new ResetDemoSite();
    $job->handle();

    assertGreaterThan(0, DB::table('songs')->where('tenant_id', 'other')->count());
    assertDatabaseHas('songs', [
        'id' => $song_to_not_delete->id,
        'title' => $song_to_not_delete->title,
    ]);
});

it('re-uploads the demo logo', function () {
    Storage::fake('global-public');

    Tenant::create('demo', 'Hypothetical Harmony', 'Australia/Perth');

    $job = new App\Jobs\ResetDemoSite();
    $job->handle();

    assertNotNull(Tenant::find('demo')->choir_logo);
    Storage::disk('global-public')->assertExists('choir-logos/'. Tenant::find('demo')->choir_logo);
});
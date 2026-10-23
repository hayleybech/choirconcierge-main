<?php

use App\Models\User;
use App\Support\VisualRegression\ScreenshotCatalog;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

test('the screenshot catalog exposes the normal authentication screenshot case', function () {
    $case = app(ScreenshotCatalog::class)->find('auth-login');

    expect($case)
        ->toBeArray()
        ->and($case['module'])->toBe('auth')
        ->and($case['page_type'])->toBe('login')
        ->and($case['state'])->toBe('normal')
        ->and($case['persona'])->toBe('guest');
});

test('catalog routes and page components are valid', function () {
    $catalog = app(ScreenshotCatalog::class);

    foreach ($catalog->cases() as $case) {
        expect(Route::has($case['route']))->toBeTrue()
            ->and(File::exists(resource_path('assets/js/Pages/'.$case['component'].'.js')))->toBeTrue();
    }

    foreach ($catalog->viewports() as $viewport) {
        expect($viewport['width'])->toBeGreaterThan(0)
            ->and($viewport['height'])->toBeGreaterThan(0);
    }
});

test('the screenshot catalog rejects path traversal IDs', function () {
    $catalogPath = storage_path('framework/testing/unsafe-visual-catalog.json');
    $catalogDirectory = dirname($catalogPath);

    File::ensureDirectoryExists($catalogDirectory);
    File::put($catalogPath, json_encode([
        'schema_version' => 1,
        'cases' => [['id' => '../outside']],
        'viewports' => [],
    ], JSON_THROW_ON_ERROR));
    config(['visual-regression.catalog_path' => $catalogPath]);

    try {
        expect(fn () => app(ScreenshotCatalog::class)->cases())
            ->toThrow(RuntimeException::class, 'safe lowercase path segments');
    } finally {
        File::delete($catalogPath);
    }
});

test('the local screenshot gallery requires authentication', function () {
    $this->get(route('local.visual-regression.index'))
        ->assertRedirect(route('login'));
});

test('an authenticated user can see catalog metadata in the local screenshot gallery', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('local.visual-regression.index'))
        ->assertOk()
        ->assertSee('Choir Gallery')
        ->assertSee('auth-login')
        ->assertSee('1440 × 900');
});

test('the gallery only serves images declared in the catalog', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('local.visual-regression.image', [
            'case' => '..',
            'viewport' => 'desktop',
            'variant' => 'baseline',
        ]))
        ->assertNotFound();
});

test('the local screenshot gallery is unavailable outside local and testing environments', function () {
    app()->detectEnvironment(fn (): string => 'production');

    $this->actingAs(User::factory()->create())
        ->get(route('local.visual-regression.index'))
        ->assertNotFound();
});

test('the gallery reports an uncaptured baseline as not found', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('local.visual-regression.image', [
            'case' => 'auth-login',
            'viewport' => 'desktop',
            'variant' => 'baseline',
        ]))
        ->assertNotFound();
});

test('the local screenshot gallery displays the latest comparison status', function () {
    $latestDirectory = storage_path('framework/testing/visual-regression-summary');
    $summaryPath = $latestDirectory.DIRECTORY_SEPARATOR.'summary.json';

    config(['visual-regression.latest_directory' => $latestDirectory]);
    File::ensureDirectoryExists($latestDirectory);
    File::put($summaryPath, json_encode([
        'schema_version' => 1,
        'results' => [[
            'case_id' => 'auth-login',
            'viewport' => 'desktop',
            'status' => 'passed',
        ]],
    ], JSON_THROW_ON_ERROR));

    try {
        $this->actingAs(User::factory()->create())
            ->get(route('local.visual-regression.index'))
            ->assertOk()
            ->assertSee('passed');
    } finally {
        File::deleteDirectory($latestDirectory);
    }
});

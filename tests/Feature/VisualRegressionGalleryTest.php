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

test('the screenshot catalog includes at least forty cases', function () {
    expect(count(app(ScreenshotCatalog::class)->cases()))->toBeGreaterThanOrEqual(40);
});

test('every indexed screenshot page has a dedicated empty state case', function () {
    $cases = collect(app(ScreenshotCatalog::class)->cases());
    $indexCases = $cases->filter(fn (array $case): bool => $case['page_type'] === 'index' && $case['state'] === 'normal');

    expect($indexCases)->not->toBeEmpty();

    foreach ($indexCases as $indexCase) {
        $emptyStateCase = $cases->first(fn (array $case): bool =>
            $case['component'] === $indexCase['component']
            && $case['page_type'] === 'index'
            && $case['state'] === 'empty'
        );

        expect($emptyStateCase)->toBeArray();
        expect($emptyStateCase['empty_props'] ?? [])->not->toBeEmpty();
    }
});

test('catalog routes and page components are valid', function () {
    $catalog = app(ScreenshotCatalog::class);

    foreach ($catalog->cases() as $case) {
        expect(Route::has($case['route']))->toBeTrue()
            ->and(File::exists(resource_path('assets/js/Pages/' . $case['component'] . '.js')))->toBeTrue();
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
        expect(fn() => app(ScreenshotCatalog::class)->cases())
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
	$response = $this->actingAs(User::factory()->create())
		->get(route('local.visual-regression.index'))
		->assertOk()
		->assertSee('<h1>CGAL Screenshot Gallery</h1>', false)
		->assertSee('auth-login');

	expect($response->getContent())->toMatch('/1440\s+×\s+900/');
});

test('the gallery shows a three-column grid with one selectable baseline or latest capture', function () {
    $baselineDirectory = storage_path('framework/testing/visual-gallery-baselines');
    $latestDirectory = storage_path('framework/testing/visual-gallery-latest');
    $caseDirectory = 'auth-login';

    config([
        'visual-regression.baseline_directory' => $baselineDirectory,
        'visual-regression.latest_directory' => $latestDirectory,
    ]);

    File::ensureDirectoryExists($baselineDirectory . DIRECTORY_SEPARATOR . $caseDirectory);
    File::ensureDirectoryExists($latestDirectory . DIRECTORY_SEPARATOR . $caseDirectory);
    File::put($baselineDirectory . DIRECTORY_SEPARATOR . $caseDirectory . DIRECTORY_SEPARATOR . 'desktop.png', 'baseline');
    File::put($latestDirectory . DIRECTORY_SEPARATOR . $caseDirectory . DIRECTORY_SEPARATOR . 'desktop.png', 'latest');

    try {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('local.visual-regression.index'))
            ->assertOk()
            ->assertSee('data-view="grid"', false)
            ->assertSee('data-gallery-view', false)
            ->assertSee('data-grid-variant-select', false)
            ->assertSee('data-comparison-captures', false)
            ->assertSee('data-image-zoom', false)
            ->assertSee('data-zoom-trigger', false)
            ->assertSee('<optgroup label="crud">', false)
            ->assertSee('<optgroup label="other">', false)
            ->assertSee('<optgroup label="central">', false)
            ->assertSee('<optgroup label="tenant">', false)
            ->assertSee('repeat(3, minmax(0, 1fr))', false)
            ->assertSee('data-grid-variant="baseline"', false)
            ->assertSee('data-grid-variant="latest"', false)
            ->assertSee(route('local.visual-regression.image', [$caseDirectory, 'desktop', 'baseline']), false)
            ->assertSee(route('local.visual-regression.image', [$caseDirectory, 'desktop', 'latest']), false);

        expect($response->getContent())->toMatch('/data-grid-variant="baseline"\s+hidden/');
    } finally {
        File::deleteDirectory($baselineDirectory);
        File::deleteDirectory($latestDirectory);
    }
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
    app()->detectEnvironment(fn(): string => 'production');

    $this->actingAs(User::factory()->create())
        ->get(route('local.visual-regression.index'))
        ->assertNotFound();
});

test('the gallery defaults to desktop normal captures and exposes the filtered screenshot count', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('local.visual-regression.index'))
        ->assertOk()
        ->assertSee('<option value="normal" selected>normal</option>', false)
        ->assertSee('<option value="Desktop" selected>Desktop</option>', false)
        ->assertSee('data-screenshot-count', false);
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
    $summaryPath = $latestDirectory . DIRECTORY_SEPARATOR . 'summary.json';

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

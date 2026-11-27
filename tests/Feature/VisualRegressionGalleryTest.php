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

test('the song show and PDF viewer are separate screenshot cases', function () {
    $catalog = app(ScreenshotCatalog::class);
    $songShow = $catalog->find('tenant-songs-show');
    $songShowPdfViewer = $catalog->find('tenant-songs-show-pdf-viewer');

    expect($songShow)
        ->toBeArray()
        ->and($songShow['pdf_viewer'] ?? false)->toBeFalse()
        ->and($songShowPdfViewer)
        ->toBeArray()
        ->and($songShowPdfViewer['component'])->toBe('Songs/Show')
        ->and($songShowPdfViewer['state'])->toBe('pdf-viewer')
        ->and($songShowPdfViewer['pdf_viewer'])->toBeTrue();
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
            ->and(collect(config('inertia.testing.page_extensions'))->contains(fn (string $extension): bool =>
                File::exists(resource_path('assets/js/Pages/' . $case['component'] . '.' . $extension))
            ))->toBeTrue();
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

test('the gallery shows a three-column grid of latest catalog captures', function () {
    $latestDirectory = storage_path('framework/testing/visual-gallery-latest');
    $caseDirectory = 'auth-login';

    config(['visual-regression.latest_directory' => $latestDirectory]);

    File::ensureDirectoryExists($latestDirectory . DIRECTORY_SEPARATOR . $caseDirectory);
    File::put($latestDirectory . DIRECTORY_SEPARATOR . $caseDirectory . DIRECTORY_SEPARATOR . 'desktop.png', 'latest');

    try {
        $response = $this->actingAs(User::factory()->create())
            ->get(route('local.visual-regression.index'))
            ->assertOk()
            ->assertSee('data-view="grid"', false)
            ->assertSee('data-image-zoom', false)
            ->assertSee('data-zoom-trigger', false)
            ->assertSee('data-zoom-help hidden', false)
            ->assertSee('<optgroup label="crud">', false)
            ->assertSee('<optgroup label="other">', false)
            ->assertSee('<optgroup label="central">', false)
            ->assertSee('<optgroup label="tenant">', false)
            ->assertSee('repeat(3, minmax(0, 1fr))', false)
            ->assertSee(route('local.visual-regression.image', [$caseDirectory, 'desktop', 'latest']), false)
            ->assertDontSee('Approved baseline');
    } finally {
        File::deleteDirectory($latestDirectory);
    }
});

test('the gallery only serves images declared in the catalog', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('local.visual-regression.image', [
            'case' => '..',
            'viewport' => 'desktop',
            'variant' => 'latest',
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
        ->assertSee('<optgroup label="Primary">', false)
        ->assertSee('<optgroup label="Secondary">', false)
        ->assertSee('<optgroup label="Other">', false)
        ->assertSee('<option value="dashboard" >Dashboard</option>', false)
        ->assertSee('<option value="mailing-lists" >Communications</option>', false)
        ->assertSee('<option value="tasks" >Onboarding</option>', false)
        ->assertSee('<option value="tenants" >Tenants</option>', false)
        ->assertSee('<option value="users" >Users</option>', false)
        ->assertSee('<option value="account" >Account Settings</option>', false)
        ->assertSee('<option value="auth" >Authentication</option>', false)
        ->assertSee('data-screenshot-count', false);
});

test('the catalog includes additional central and tenant page templates', function () {
    $caseIds = collect(app(ScreenshotCatalog::class)->cases())->pluck('id');

    expect($caseIds)->toContain(
        'central-users-index',
        'central-users-index-empty',
        'central-tenants-index',
        'central-tenants-index-empty',
        'tenant-folders-edit',
        'tenant-polls-edit',
        'tenant-riser-stacks-create',
        'tenant-riser-stacks-edit',
        'tenant-riser-stacks-show',
        'tenant-roles-edit',
    );
});

test('the gallery does not serve retired baseline images', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('local.visual-regression.image', [
            'case' => 'auth-login',
            'viewport' => 'desktop',
            'variant' => 'baseline',
        ]))
        ->assertNotFound();
});

test('the local screenshot gallery reports whether a capture is available', function () {
    $latestDirectory = storage_path('framework/testing/visual-regression-captures');

    config(['visual-regression.latest_directory' => $latestDirectory]);
    File::ensureDirectoryExists($latestDirectory . DIRECTORY_SEPARATOR . 'auth-login');
    File::put($latestDirectory . DIRECTORY_SEPARATOR . 'auth-login' . DIRECTORY_SEPARATOR . 'desktop.png', 'capture');

    try {
        $this->actingAs(User::factory()->create())
            ->get(route('local.visual-regression.index'))
            ->assertOk()
            ->assertSee('captured');
    } finally {
        File::deleteDirectory($latestDirectory);
    }
});

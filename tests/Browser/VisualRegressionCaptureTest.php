<?php

use App\Support\VisualRegression\ScreenshotCatalog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Tests\Browser\VisualRegressionTestCase;

uses(VisualRegressionTestCase::class)->group('visual');

test('captures every catalog case at every viewport without JavaScript errors', function () {
    $catalog = app(ScreenshotCatalog::class);
    $latestDirectory = config('visual-regression.latest_directory');
    $baselineDirectory = config('visual-regression.baseline_directory');

    expect(app()->environment())->toBe('testing')
        ->and(config('database.connections.mysql.database'))
        ->toMatch('/^choirconcierge_test(?:_[0-9]+(?:\.[0-9]+)?)?$/');

    config(['app.debug' => false]);
    Carbon::setTestNow('2026-10-23 12:00:00');

    try {
        foreach ($catalog->cases() as $case) {
            foreach ($catalog->viewports() as $viewport) {
                File::ensureDirectoryExists($latestDirectory.DIRECTORY_SEPARATOR.$case['id']);
                File::ensureDirectoryExists($baselineDirectory.DIRECTORY_SEPARATOR.$case['id']);

                $page = visit(route($case['route'], $case['parameters'], false))
                    ->resize($viewport['width'], $viewport['height']);

                $page->assertSee($case['assert_text']);
                $page->script("document.head.insertAdjacentHTML('beforeend', '<style>*{animation:none!important;transition:none!important;font-family:Arial,sans-serif!important;}</style>')");
                $page->wait(0.25)
                    ->assertNoJavaScriptErrors()
                    ->screenshot(true, 'Latest/'.$case['id'].'/'.$viewport['id'].'.png');
            }
        }
    } finally {
        Carbon::setTestNow();
    }
});
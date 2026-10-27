# Visual regression testing

The visual-regression suite uses Pest's browser plugin and Playwright to capture the cases declared in `resources/visual-regression/catalog.json`. It checks that each page renders the expected Inertia component and text, reports JavaScript errors, captures full-page screenshots in Chromium at the catalog's desktop, tablet, and mobile viewports, and can compare those captures with reviewed baselines.

## Prerequisites

- Install the project's PHP and JavaScript dependencies with `composer install` and `npm install`.
- Configure `.env.testing` to use a dedicated MySQL test database. Browser tests require `APP_ENV=testing`, the MySQL connection, and a configured database name; they refresh the test database and create synthetic screenshot fixtures. Do not point `.env.testing` at production or everyday local data.
- Install Playwright's Chromium browser once with `composer visual:install`.
- The capture runner builds the frontend before running the browser test.

## Common commands

From the repository root:

```sh
# Capture every catalog case at every viewport.
composer visual:test

# Capture and compare against approved baselines.
composer visual:compare

# Check the catalog against statically rendered Inertia page components.
composer visual:audit

# Run the visual runner, audit, and gallery JavaScript tests.
npm run test:visual
```

`composer visual:test` is the local capture entry point. Visual regression tests are currently disabled in CI. A normal local run captures screenshots but does not compare against baselines; use `composer visual:compare` to compare captures with approved baselines and generate diffs. Both commands replace the previous `Latest` and `Diffs` output, so save any artifacts you need before running them again.

To capture one case while diagnosing it, set `VISUAL_REGRESSION_CASE_ID` and run the Pest browser test directly after building the frontend. For example, in PowerShell:

```powershell
$env:VISUAL_REGRESSION_CASE_ID = 'tenant-songs-show'
php artisan test --compact tests/Browser/VisualRegressionCaptureTest.php
Remove-Item Env:VISUAL_REGRESSION_CASE_ID
```

The filtered Pest run writes the selected case to `Latest`; use `composer visual:test` for a complete capture run.

## CI status

Visual regression tests are not currently run by the CI pipeline. The runner's strict CI comparison mode remains available for when a stable canonical CI renderer is ready: `VISUAL_REGRESSION_CI=true composer visual:test` performs the full catalog audit and fails for missing baselines or any differing pixels, including image-dimension changes.

## Updating approved baselines

For now, generate and review baselines locally after intentional visual changes. Use a consistent local browser, operating system, fonts, and rendering dependencies as the canonical renderer. The command updates `Baselines` from newly captured images without enabling strict CI catalog checks; ordinary test runs never update them automatically:

```sh
VISUAL_REGRESSION_CANONICAL=true composer visual:update
```

In PowerShell, set `$env:VISUAL_REGRESSION_CANONICAL = 'true'` before running `composer visual:update`. Review the generated baseline image changes alongside the UI change, then commit the approved images in `tests/Browser/Screenshots/Baselines`. Do not commit `Latest` or `Diffs`.

## Catalog and output

- Add or update cases in `resources/visual-regression/catalog.json`. Each case declares its route, expected component, synthetic persona, state, and filter metadata; the catalog also defines browser and viewport profiles.
- Run `composer visual:audit` when adding a page. It reports rendered components missing from the catalog, catalog components not found in static render calls, and dynamic render calls requiring review. Catalog coverage is per component; it does not enumerate every route URL, role, or possible page state.
- Current coverage gap: the audit reports `Central/Announcements/Create` as missing. Local captures continue with a warning, but `VISUAL_REGRESSION_CI=true` makes the runner stop until the catalog covers it.
- Approved references live in `tests/Browser/Screenshots/Baselines` and are versioned. Captures, comparison diffs, and the machine-readable run summary are written under `tests/Browser/Screenshots/Latest` and `tests/Browser/Screenshots/Diffs`; these run outputs are ignored by Git.
- The authenticated CGAL gallery is available only in local and testing environments at `/app/cgal`. It filters the catalog and displays available baseline, latest-capture, and diff images.
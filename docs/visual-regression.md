# Visual regression testing

The screenshot suite uses Pest's browser plugin and Playwright to capture every case and viewport in `resources/visual-regression/catalog.json`. Argos owns visual baselines, comparison, and pull-request review; screenshots and generated diffs are no longer committed.

## Local capture

- Install PHP and JavaScript dependencies with `composer install` and `npm ci`.
- Configure `.env.testing` to use a dedicated MySQL test database. Browser tests refresh the test database and create synthetic fixtures; never point it at production or everyday local data.
- Install Playwright Chromium with `composer visual:install`.
- Capture the complete catalog with `composer visual:test`. This builds the frontend, runs the browser capture, and verifies that exactly one PNG exists for every case/viewport pair in `tests/Browser/Screenshots/ArgosCaptures`.
- Capture workers default to two; set `VISUAL_REGRESSION_CONCURRENCY` to a positive integer to adjust the bound for available browser and database resources.
- Run `composer visual:audit` to check catalog coverage against statically rendered Inertia page components, and `npm run test:visual` for the runner, audit, and gallery tests.
- To diagnose one case, set `VISUAL_REGRESSION_CASE_ID` and run `php artisan test --compact tests/Browser/VisualRegressionCaptureTest.php` after building the frontend.

The local/testing-only gallery at `/app/cgal` remains available for browsing and filtering the most recent catalog captures. Its images are generated output and should not be committed.

## ChipperCI and Argos

The ChipperCI pipeline runs the catalog audit, builds the app, captures the full catalog using its MySQL and Dusk/Chrome services, validates capture completeness, then uploads the PNG directory with `npm exec -- argos upload tests/Browser/Screenshots/ArgosCaptures`. A missing token, incomplete capture, missing commit metadata, or failed CLI upload fails the pipeline step.

Configure `ARGOS_TOKEN` as a protected ChipperCI project environment secret; never put the token in `.chipperci.yml`, `.env.example`, or source code. Argos CLI does not list ChipperCI as an automatically detected provider, so the pipeline maps ChipperCI's full `CI_COMMIT_SHA` and `CI_COMMIT_BRANCH` to `ARGOS_COMMIT` and `ARGOS_BRANCH`. The hosted Argos/SCM integration must associate the uploaded commit with its pull request.

After the first successful Argos upload, select the Argos visual status check in the repository's pull-request branch protection settings and mark it required. Review and accept visual changes in Argos before merging. The required-check setting is repository-host configuration and cannot be enforced by this code repository.

## Catalog

Add or update cases in `resources/visual-regression/catalog.json`. Each case defines its route, expected Inertia component, synthetic persona, state, and filter metadata; the catalog also defines browser and viewport profiles. The audit verifies component coverage, not every possible route, role, or page state.
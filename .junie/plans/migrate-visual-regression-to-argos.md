---
sessionId: session-261027-213227-1et9
---

# Requirements

### Overview & Goals
Move screenshot comparison and review from the repository’s custom baseline/diff workflow to Argos CI.

### Scope
- Keep ChipperCI as the CI provider and preserve all cases and viewport profiles in `resources/visual-regression/catalog.json`.
- Capture screenshots during CI and upload them to Argos using a protected ChipperCI secret; never store the token in the repository.
- Make Argos visual review a required pull-request merge check.
- Retire the checked-in-baseline comparison/update workflow and its generated diff artifacts.

### Acceptance Criteria
- ChipperCI runs catalog coverage audit, builds the app, captures the full catalog at every configured viewport, and uploads the captures to Argos.
- A missing/invalid Argos credential or failed capture/upload fails the visual CI job.
- Argos’s required status check prevents merging until the visual result is accepted.
- The local catalog audit and relevant coverage remain available; no screenshot cases or viewports are lost.

# Technical Design

### Current Implementation
- `.chipperci.yml` provisions PHP 8.4 and Node 22, installs dependencies/builds assets, then runs PHP and JS tests; it has no browser screenshot stage today.
- `resources/visual-regression/catalog.json` defines screenshot cases and viewports. `scripts/visual-regression-runner.js` drives catalog capture, and `scripts/visual-regression-audit.js` checks catalog coverage against statically rendered Inertia components.
- `package.json` exposes `visual:compare` and `visual:update`; Composer aliases and `docs/visual-regression.md` describe the local baseline/diff process. Existing visual tests cover the runner, audit, and gallery.

### Proposed Changes
- Keep screenshot capture in `scripts/visual-regression-runner.js`; it already drives the catalog and is independent of the baseline comparison. Write each captured PNG into a dedicated upload directory with deterministic names containing both catalog case ID and viewport ID, and verify that the output count matches `cases × viewports` before upload.
- Use Argos's framework-agnostic CLI integration: add `@argos-ci/cli` as a development dependency, then invoke `npm exec -- argos upload <capture-directory>` after the full capture succeeds. The CLI—not a new Playwright reporter or a custom HTTP client—uploads the image files and creates the Argos build.
- Add a ChipperCI visual pipeline step in `.chipperci.yml` to install Chromium, run the catalog audit and capture, and run the CLI upload. Supply `ARGOS_TOKEN` from a protected ChipperCI secret through the step environment; do not put a token in source or logs. Confirm ChipperCI's available commit/branch/pull-request metadata variables during implementation; rely on Argos automatic CI detection if supported, otherwise map the provider's variables to Argos's documented branch/commit inputs so PR screenshots compare against the correct base build.
- Remove baseline comparison/update commands and implementation, baseline-only tests/assets, and revise `docs/visual-regression.md` to document local capture, CI upload, secret provisioning, and Argos review. Keep capture/audit functionality and any useful catalog/gallery behavior that does not depend on baselines.
- Configure the Argos status check as required in the repository’s pull-request protection settings; this is external configuration, not a committed secret or code change.

### Risks
- Browser dependencies, database fixtures, and application startup may make the ChipperCI visual stage resource-intensive; reuse the existing runner’s setup assumptions and ensure the pipeline provisions them explicitly.
- If ChipperCI is not auto-detected by Argos, incomplete branch/commit/pull-request metadata could prevent correct base-build selection; validate the provider's predefined variables and map the documented Argos inputs explicitly.
- Required-check enforcement depends on selecting the correct Argos check name in repository protection settings after the first successful pipeline run.

# Testing

### Validation Approach
- Exercise the runner and catalog audit tests, updating assertions to reflect Argos upload preparation and the removal of baseline comparison behavior.
- Verify the capture directory contains exactly one PNG per catalog case/viewport pair, with unique deterministic names, before the CLI is invoked.
- Validate the CLI upload succeeds with `ARGOS_TOKEN`, fails on missing/invalid credentials or upload errors, and receives enough CI metadata for Argos to associate the build with the correct commit and pull request; ensure credentials are not logged or committed.
- Confirm the Argos pull-request status is configured as a required check.

# Delivery Steps

### ✓ Step 1: Prepare catalog captures for Argos
The existing catalog runner produces a complete directory of deterministically named PNGs for upload.
- Update `scripts/visual-regression-runner.js` to write to a dedicated Argos capture directory and name files with catalog case and viewport IDs.
- Validate output completeness against every catalog case and viewport before an upload can proceed.
- Preserve `resources/visual-regression/catalog.json` and `scripts/visual-regression-audit.js`; update runner tests for naming, output counts, and incomplete-capture failure.

### ✓ Step 2: Add ChipperCI capture and Argos CLI upload
ChipperCI uploads the full catalog capture directory as an Argos build using a protected project token.
- Add `@argos-ci/cli` as a development dependency and invoke `npm exec -- argos upload <capture-directory>` after capture; use the CLI rather than the Playwright reporter or custom upload code.
- Extend `.chipperci.yml` with a visual step that provisions the runner's database/application and Chromium requirements, runs the audit and full capture, then uploads only after completeness validation.
- Map the protected ChipperCI secret to `ARGOS_TOKEN`; validate the provider's CI metadata and explicitly provide Argos branch/commit context if automatic ChipperCI detection is unavailable.
- Ensure missing credentials, incomplete output, failed CLI uploads, or insufficient build context fail the step without exposing the token.

### ! Step 3: Retire baseline comparison and document Argos review
The custom comparison lifecycle is removed and the documented workflow matches Argos-based review.
- Remove the `visual:compare` and `visual:update` commands and baseline/diff-specific implementation and tests, while retaining capture and audit entry points.
- Remove checked-in baseline artifacts and baseline-only gallery assumptions; preserve unrelated visual gallery behavior where appropriate.
- Rewrite `docs/visual-regression.md` to describe local capture, CI upload, secret provisioning, retained catalog coverage, and Argos approval.
- Configure the resulting Argos status check as required in pull-request branch protection and verify the merge-gate behavior.


### Playwright Reporter Trade-off
- Keep the CLI upload approach for this migration because screenshots are currently produced by the Laravel/Pest browser flow and catalog runner, not Playwright Test. Argos's Playwright reporter expects Playwright Test and captures through `argosScreenshot()`; using it would require porting catalog cases, app/persona setup, expected-component/text checks, and JavaScript-error reporting.
- The reporter could reduce upload plumbing and provide Playwright traces/failure artifacts, but it would not replace `scripts/visual-regression-audit.js`'s catalog coverage guarantee. A runner conversion is intentionally out of scope unless separately approved.
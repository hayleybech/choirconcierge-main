---
sessionId: session-261027-213227-1et9
---

# Requirements

### Overview & Goals
Reduce the runtime of the catalog-driven visual capture by running independent browser captures concurrently while retaining full coverage and Argos CI.

### Scope
- Keep ChipperCI and preserve all cases and viewport profiles in `resources/visual-regression/catalog.json`.
- Add bounded parallel browser execution within the existing Pest capture flow; do not use CI job sharding or migrate to Playwright Test.
- Make each concurrent capture safe against shared fixture, database, tenancy, browser, and screenshot-output state.
- Capture screenshots during CI and upload them to Argos using a protected ChipperCI secret; never store the token in the repository.
- Make Argos visual review a required pull-request merge check.
- Retire the checked-in-baseline comparison/update workflow and its generated diff artifacts.

### Acceptance Criteria
- The runner audits the catalog, launches a configurable bounded number of browser captures, and uploads only after every case/viewport succeeds.
- Workers produce unique deterministic paths with no missing/duplicate images; failures prevent partial Argos upload.
- Existing browser assertions and teardown behavior remain intact, with measured runtime improvement under CI-like resource limits.
- A missing/invalid Argos credential or failed capture/upload fails the visual CI job.
- Argos’s required status check prevents merging until the visual result is accepted.
- The local catalog audit and relevant coverage remain available; no screenshot cases or viewports are lost.

# Technical Design

### Current Implementation
- `.chipperci.yml` already has an Argos visual stage that installs Chromium, runs the audit and `composer visual:test`, then uploads the completed `ArgosCaptures` directory.
- `resources/visual-regression/catalog.json` defines screenshot cases and viewports; `scripts/visual-regression-runner.js` invokes one Pest capture test and validates the complete output. `scripts/visual-regression-audit.js` checks catalog coverage against statically rendered Inertia components.
- `tests/Browser/VisualRegressionCaptureTest.php` loops through cases sequentially, creating reusable persona/tenant fixtures and capturing configured viewports. `tests/Browser/VisualRegressionTestCase.php` configures and restores browser-server session/domain state. The runner and browser tests currently assume one process.

### Proposed Changes
- Extend `scripts/visual-regression-runner.js` with a bounded coordinator that partitions independent catalog cases, runs captures concurrently, waits for all workers, and performs one exact completeness check before Argos upload.
- Refactor `tests/Browser/VisualRegressionCaptureTest.php` and `tests/Browser/VisualRegressionTestCase.php` so every worker owns its browser and fixture lifecycle; prevent shared MySQL, tenancy, uploaded-file, cleanup, and screenshot-path conflicts.
- Preserve catalog audit, page/component/text/console assertions, Argos CLI upload, and protected token behavior. Add configurable concurrency with a conservative CI default.
- Use Argos's framework-agnostic CLI integration: add `@argos-ci/cli` as a development dependency, then invoke `npm exec -- argos upload <capture-directory>` after the full capture succeeds. The CLI—not a new Playwright reporter or a custom HTTP client—uploads the image files and creates the Argos build.
- Add a ChipperCI visual pipeline step in `.chipperci.yml` to install Chromium, run the catalog audit and capture, and run the CLI upload. Supply `ARGOS_TOKEN` from a protected ChipperCI secret through the step environment; do not put a token in source or logs. Confirm ChipperCI's available commit/branch/pull-request metadata variables during implementation; rely on Argos automatic CI detection if supported, otherwise map the provider's variables to Argos's documented branch/commit inputs so PR screenshots compare against the correct base build.
- Remove baseline comparison/update commands and implementation, baseline-only tests/assets, and revise `docs/visual-regression.md` to document local capture, CI upload, secret provisioning, and Argos review. Keep capture/audit functionality and any useful catalog/gallery behavior that does not depend on baselines.
- Configure the Argos status check as required in the repository’s pull-request protection settings; this is external configuration, not a committed secret or code change.

### Risks
- The capture test creates shared tenant/persona fixtures and teardown cleanup; concurrent workers require isolated data to prevent cross-worker state contamination.
- Browser processes and database connections consume substantial CI resources; concurrency must be bounded and validated by runtime/stability measurements.
- Stale artifacts after retries or partial failures must not pass validation; clear output once before coordination and validate exact expected paths after all workers complete.
- If ChipperCI is not auto-detected by Argos, incomplete branch/commit/pull-request metadata could prevent correct base-build selection; validate the provider's predefined variables and map the documented Argos inputs explicitly.
- Required-check enforcement depends on selecting the correct Argos check name in repository protection settings after the first successful pipeline run.

# Testing

### Validation Approach
- Test deterministic, complete/disjoint catalog partitioning, worker limits, output paths, and failure handling in `scripts/visual-regression-runner.test.js`.
- Verify worker isolation and teardown, preserve all existing browser assertions, and confirm exactly one PNG per case/viewport before the existing CLI upload.
- Compare full-catalog sequential and bounded-parallel runtimes under CI-like conditions, and select the default based on observed improvement and stability.
- Confirm the Argos pull-request status is configured as a required check.

# Delivery Steps

### ✓ Step 1: Make catalog work partitionable and outputs concurrency-safe
The runner assigns every case to bounded workers without overlap or loss.
- Update `scripts/visual-regression-runner.js` to partition catalog cases deterministically and coordinate a configurable worker limit.
- Preserve one-time output cleanup and unique `ArgosCaptures/<case-id>/<viewport-id>.png` paths.
- Extend `scripts/visual-regression-runner.test.js` for complete/disjoint partitions, concurrency bounds, stable naming, and worker failure propagation.

### ✓ Step 2: Isolate concurrent browser capture state
Independent browser workers finish their cases without sharing mutable fixtures or cleanup state.
- Refactor `tests/Browser/VisualRegressionCaptureTest.php` and `tests/Browser/VisualRegressionTestCase.php` for independent browser contexts and safe fixture lifecycle.
- Prevent concurrent conflicts across MySQL test records, tenancy/personas, uploaded files, and screenshots.
- Preserve existing assertions and test isolation, teardown, and failure behavior.

### ✓ Step 3: Integrate bounded capture with Argos
ChipperCI uploads a complete parallel capture, with the worker limit supported by measured CI behavior.
- Keep `.chipperci.yml` on its existing audit → capture → Argos CLI upload flow; upload only after all workers succeed and exact completeness passes.
- Choose a conservative configurable CI concurrency default and verify it does not destabilize database or browser workers.
- Compare full-catalog sequential and parallel runtime under CI-like conditions; failed or incomplete worker runs must block upload.
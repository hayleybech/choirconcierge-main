<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

## Foundational Context

This application is a Laravel application running on PHP 8.4. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If a frontend change doesn't show in the UI or you get a "Unable to locate file in Vite manifest" error, run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Use `search-docs` before changes that depend on Laravel ecosystem APIs, behavior, configuration, or version-specific syntax. Skip it for copy-only edits and other changes where package documentation is irrelevant. Reuse sufficient results already in context instead of searching again.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists, including path-scoped framework guidelines under `.ai/rules/boost`. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Follow existing application Enum naming conventions.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.
- Activate the `deploying-to-cloud` skill whenever deploying to Laravel Cloud, configuring Cloud environments or resources, using the Cloud CLI, or troubleshooting Cloud deployments.

=== herd rules ===

# Laravel Herd

- The application is served by Laravel Herd at `https?://[kebab-case-project-dir].test`. Use the `get-absolute-url` tool to generate valid URLs. Never run commands to serve the site. It is always available.
- Use the `herd` CLI to manage services, PHP versions, and sites (e.g. `herd sites`, `herd services:start <service>`, `herd php:list`). Run `herd list` to discover all available commands.

=== tests rules ===

# Test Enforcement

- Add or update tests for behavior and logic changes when a test provides meaningful regression coverage.
- Pure copy, styling, and layout-only changes do not require new or updated tests.
- When test coverage applies, run the affected tests and ensure they pass.
- Test the changed behavior and its important failure modes, but do not add tests beyond them.
- Read the `testing-best-practices` skill before writing tests.

=== inertia-laravel/core rules ===

# Inertia

- Inertia creates fully client-side rendered SPAs without modern SPA complexity, leveraging existing server-side patterns.
- Components live in `resources/js/Pages` (unless specified in `vite.config.js`). Use `Inertia::render()` for server-side routing instead of Blade views.
- ALWAYS use `search-docs` tool for version-specific Inertia documentation and updated code examples.
- IMPORTANT: Activate `inertia-react-development` when working with Inertia client-side patterns.

# Inertia v1

- Inertia v1 does not support the following v2 features: deferred props, infinite scrolling (merging props + `WhenVisible`), lazy loading on scroll, polling, or prefetching. Do not use these.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

=== laravel/v11 rules ===

# Laravel 11

- CRITICAL: ALWAYS use `search-docs` tool for version-specific Laravel documentation and updated code examples.
- This project upgraded from Laravel 10 without migrating to the new streamlined Laravel 11 file structure.
- This is perfectly fine and recommended by Laravel. Follow the existing structure from Laravel 10. We do not need to migrate to the Laravel 11 structure unless the user explicitly requests it.

## Laravel 10 Structure

- Middleware typically lives in `app/Http/Middleware/` and service providers in `app/Providers/`.
- There is no `bootstrap/app.php` application configuration in a Laravel 10 structure:
    - Middleware registration is in `app/Http/Kernel.php`
    - Exception handling is in `app/Exceptions/Handler.php`
    - Console commands and schedule registration is in `app/Console/Kernel.php`
    - Rate limits likely exist in `RouteServiceProvider` or `app/Http/Kernel.php`

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.

- Laravel 11 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

## New Artisan Commands

- List Artisan commands using Boost's MCP tool, if available. New commands available in Laravel 11:
    - `php artisan make:enum`
    - `php artisan make:class`
    - `php artisan make:interface`

=== pest/core rules ===

# Pest

- This project uses Pest. Create tests with `php artisan make:test --pest {name}`.
- Do not include the test suite directory in `{name}`. Use `SomeFeatureTest`, not `Feature/SomeFeatureTest`.
- Read the `testing-best-practices` skill for guidance on coverage, naming, structure, dependency isolation, and review.
- Do not delete tests or test files without approval. They are part of the application.

## Running Tests

- Run the narrowest set of tests that covers the change. Pass a file path or `--filter=testName` to `php artisan test --compact`.
- Rerun a test after each change to it.
- Run `vendor/bin/pest` to call the test runner directly. It accepts the same file path and `--filter=testName` arguments.
- After the feature tests pass, ask the user to run the complete suite with `php artisan test --compact`.

=== inertia-react/core rules ===

# Inertia + React

- IMPORTANT: Activate `inertia-react-development` when working with Inertia React client-side patterns.

=== spatie/laravel-medialibrary/core rules ===

## Media Library

- `spatie/laravel-medialibrary` associates files with Eloquent models, with support for collections, conversions, and responsive images.
- Always activate the `medialibrary-development` skill when working with media uploads, conversions, collections, responsive images, or any code that uses the `HasMedia` interface or `InteractsWithMedia` trait.

</laravel-boost-guidelines>

## Project-Specific Guidance

This section contains repository-specific information maintained alongside the generated Laravel Boost guidelines above. When package versions or paths differ, the dependency manifests and actual project structure are the source of truth.

### Controller and Collection Practices

- When reading request values in a controller, use validated data rather than raw input.
- If resolving an N+1 issue would require a larger refactor, ask before proceeding.
- Prefer Laravel collections over PHP array functions.

### Commands

#### PHP / Laravel

```sh
php artisan test --compact
php artisan test --compact --filter=SingerControllerTest
vendor/bin/phpstan analyse
php artisan app:create-test-tenant
php artisan queue:work
```

`php artisan schedule:test` is an interactive command; use it only when an interactive run is explicitly intended.

#### Frontend (Laravel Mix, not Vite)

```sh
npm run dev
npm run watch
npm run prod
npm test
npm run prettier
```

There is no `npm run build` script. For tests, create a `choirconcierge_test` database and configure `.env.testing` to use it. `phpunit.xml` configures the test queue, cache, mail, Telescope, and stop-on-failure behavior.

### Project Architecture

Choir Concierge is a multi-tenant SaaS platform for choir management, including member rosters, song libraries, event RSVP and attendance, document storage, riser-stack editing, mailing lists, onboarding checklists, and polls.

- **Runtime and frontend:** Composer requires PHP `>=8.4`; the application uses Laravel 11 with the Laravel 10 file structure. The frontend uses Inertia.js v1, React 17, and Tailwind CSS 4, bundled with Laravel Mix. Inertia pages are under `resources/assets/js/Pages/`.
- **Database and queues:** MySQL is shared across tenants; tenants do not have separate databases. The queue driver is `database`, and background jobs implement `ShouldQueue`.
- **Billing:** Laravel Spark with Paddle (`laravel/spark-paddle`); `Tenant` is billable.
- **Tenancy:** `stancl/tenancy` v3 uses single-database mode. `DatabaseTenancyBootstrapper` is intentionally disabled; Cache, Filesystem, and Queue bootstrappers are active. Tenants are identified by human-readable slugs in paths (`choirconcierge.com/{tenant}/...`); subdomain requests redirect to that format. Models use `BelongsToTenant` for automatic scoping. `TenantCreated` starts a chained pipeline: `SendWelcomeEmailSeries`, `SendTenantCreatedNotification`, `SeedForTenant`, then `CreateAdminMembershipForTenant`.

#### Routes

| File | Domain |
|---|---|
| `routes/web.php` | Central routes: login, 2FA, super-admin dashboard, and tenant management |
| `routes/tenant.php` | Choir features behind `InitializeTenancyByPath` middleware |
| `routes/api.php` | Sanctum-authenticated API |
| `routes/channels.php` | Broadcasting channels |

#### Domain Models

| Model | Role |
|---|---|
| `Tenant` | Choir organisation; billable |
| `Ensemble` | Sub-group within a tenant |
| `User` | Shared across tenants; TOTP 2FA via `laragear/two-factor` |
| `Membership` | User-to-tenant join; holds roles and `SingerStatus` |
| `Enrolment` | Membership-to-ensemble join |
| `Role` | Tenant-scoped role with JSON `abilities` |
| `Song` | Song library; `LearningStatus` tracked per member |
| `Event` | Rehearsal or performance with RSVP, attendance, and recurrence |
| `UserGroup` | Mailing list with sender/recipient role and ensemble filters |
| `MailLog` | Sent/received email tracking, including an open-tracking pixel via signed URL |
| `RiserStack` | SVG-based visual riser arrangement using SnapSVG on the frontend |
| `Task` | Onboarding task assigned to members |
| `Poll` | Internal voting with an open/close lifecycle |
| `CustomField` | Tenant-defined extra fields on memberships |

### Authorization and Inertia Data

- Laravel policies are in `app/Policies/` and `app/Models/Policies/`.
- `Membership::hasAbility(string)` checks the role's JSON abilities.
- The super-admin is identified by a single email in `User::isSuperAdmin()`.
- `HandleInertiaRequests::share()` supplies a `can` map of permission gates to every Inertia page.
- Shared Inertia data includes `can`, `tenant`, `user`, `navigation`, `flash`, `googleApiKey`, `impersonationActive`, and `userChoirs`.

### Mail and Scheduled Jobs

- Outbound mail uses Mailgun through `mailgun/mailgun-php` and `symfony/mailgun-mailer`; mailing lists are managed through the Mailgun API.
- Inbound mail uses `webklex/laravel-imap` to poll a group mailbox every minute with the scheduled `ProcessGroupMailbox` job.
- Notifications that create mail log entries use the `LogsToMailLog` trait.
- Other key scheduled jobs include `MarkAbsencesAfterEvents` and `SendAttendanceReports` hourly, plus daily backups and Telescope pruning.

### Testing Conventions

- Tests use Pest. Prefer feature tests; each test class creates a fresh `phpunit` tenant.
- `actingAsRole('Admin')` creates a user with that role in the test tenant.
- Use factories and their existing states when creating test models.

### Billing Plans and Timezone Helpers

- Paddle has three yearly plans: Small (up to 25 users), Medium (up to 50 users), and Large (unlimited). The `has_gratis` flag in the tenant's `data` JSON column bypasses billing checks.
- Use `tz_from_tenant_to_utc()` and `tz_from_utc_to_tenant()` in `app/helpers.php` for timezone conversion; do not use raw Carbon shifts.
- Apply `TenantTimezoneDates` to models with date columns that need tenant-aware display.
- `spatie/laravel-query-builder` powers filterable and sortable index endpoints; custom sorts are in `app/Http/Sorts/`.

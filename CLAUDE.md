# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

> Also read **`AGENTS.md`** — it contains Laravel Boost guidelines covering PHP conventions, Inertia v1 patterns, Tailwind CSS, Pest testing, Media Library, and Laravel 11 nuances. Those rules take priority over general intuition.

---

## Commands

### PHP / Laravel
```bash
php artisan test --compact                                 # run all tests
php artisan test --compact --filter=SingerControllerTest  # run a single test class or method
./vendor/bin/phpstan analyse                               # static analysis (level 5, uses phpstan.neon)
php artisan app:create-test-tenant                         # seed a local tenant for dev
php artisan queue:work                                     # process queued jobs locally
php artisan schedule:test                                  # interactively run a scheduled command
```

### Frontend (Laravel Mix — not Vite)
```bash
npm run dev    # one-off dev build
npm run watch  # watch mode
npm run prod   # production build
npm test       # Jest unit tests
npm run prettier  # format JS/TS
```

### Test environment setup
- Create a `choirconcierge_test` database and copy `.env` → `.env.testing` pointing at it.
- `phpunit.xml` sets `QUEUE_DRIVER=sync`, `CACHE_DRIVER=array`, `MAIL_MAILER=log`, `TELESCOPE_ENABLED=false`, `stopOnFailure=true`.

---

## Architecture overview

**Choir Concierge** is a multi-tenant SaaS platform for choir management: member rosters, song libraries, event RSVP/attendance, document storage, riser-stack editing, mailing lists (inbound + outbound), onboarding checklists, and polls.

### Stack
- **PHP 8.2 / Laravel 11** — but using the **Laravel 10 file structure** (no `bootstrap/app.php`). Middleware is in `app/Http/Kernel.php`, schedule in `app/Console/Kernel.php`, exceptions in `app/Exceptions/Handler.php`.
- **Frontend**: Inertia.js v1 + React 17 + Tailwind CSS v3, bundled with **Laravel Mix** (webpack), not Vite. Pages live in `resources/assets/js/Pages/`.
- **Database**: MySQL, single shared database for all tenants (no per-tenant DBs).
- **Queue**: database driver; background jobs implement `ShouldQueue`.
- **Billing**: Laravel Spark + Paddle (`laravel/spark-paddle`). The `Tenant` model is `Spark\Billable`.

### Multi-tenancy (`stancl/tenancy` v3)
- **Single-database mode** — `DatabaseTenancyBootstrapper` is intentionally commented out. Active bootstrappers: Cache, Filesystem, Queue.
- **Path-based identification**: `choirconcierge.com/{tenant}/...`. Subdomain requests are redirected to the path format. Tenant IDs are human-readable slugs.
- Models use the `BelongsToTenant` trait for automatic scoping.
- On `TenantCreated`, a chained job pipeline runs: `SendWelcomeEmailSeries → SendTenantCreatedNotification → SeedForTenant → CreateAdminMembershipForTenant`.

### Route structure
| File | Domain |
|---|---|
| `routes/web.php` | Central routes — login, 2FA, super-admin dashboard, tenant management |
| `routes/tenant.php` | All choir features behind `InitializeTenancyByPath` middleware |
| `routes/api.php` | API (Sanctum-authenticated) |
| `routes/channels.php` | Broadcasting channels |

### Key domain models
| Model | Role |
|---|---|
| `Tenant` | Choir organisation; billable |
| `Ensemble` | Sub-group within a Tenant |
| `User` | Shared across tenants; TOTP 2FA via `laragear/two-factor` |
| `Membership` | User ↔ Tenant join (formerly "Singer"); holds roles + `SingerStatus` |
| `Enrolment` | Membership ↔ Ensemble |
| `Role` | Tenant-scoped with JSON `abilities` array |
| `Song` | Song library; `LearningStatus` tracked per member |
| `Event` | Rehearsal/performance with RSVP, attendance, recurrence |
| `UserGroup` | Mailing list with sender/recipient role and ensemble filters |
| `MailLog` | Tracks sent/received emails; open-tracking pixel via signed URL |
| `RiserStack` | SVG-based visual riser arrangement (SnapSVG on frontend) |
| `Task` | Onboarding task assigned to members |
| `Poll` | Internal voting with open/close lifecycle |
| `CustomField` | Tenant-defined extra fields on memberships |

### Authorization
- Laravel Policies in `app/Policies/` and `app/Models/Policies/`.
- `Membership::hasAbility(string)` checks against role abilities JSON.
- Super-admin: single email checked in `User::isSuperAdmin()`.
- `HandleInertiaRequests::share()` pushes a `can` map (all permission gates) to every Inertia page.

### Inertia shared data
Every page load receives: `can`, `tenant`, `user`, `navigation`, `flash`, `googleApiKey`, `impersonationActive`, `userChoirs`.

### Mail
- **Outbound**: Mailgun via `mailgun/mailgun-php` + `symfony/mailgun-mailer`. Mailing lists managed through Mailgun API.
- **Inbound**: `webklex/laravel-imap` polls a group mailbox every minute (`ProcessGroupMailbox` scheduled job).
- Notifications that create mail log entries use the `LogsToMailLog` trait.

### Scheduled jobs (key ones)
| Job | Frequency |
|---|---|
| `ProcessGroupMailbox` | Every minute |
| `MarkAbsencesAfterEvents` | Hourly |
| `SendAttendanceReports` | Hourly |
| Backups, Telescope prune | Daily |

### Testing conventions
- Every test class creates a fresh `phpunit` tenant.
- `actingAsRole('Admin')` helper creates a user with that role in the test tenant.
- Use `php artisan make:test --pest {name}` for feature tests; add `--unit` for unit tests.
- Prefer feature tests over unit tests.

### Billing plans
Three yearly Paddle plans (Small ≤25 users, Medium ≤50 users, Large unlimited). `has_gratis` flag in the tenant's `data` JSON column bypasses billing checks.

### Helpers & traits
- `tz_from_tenant_to_utc()` / `tz_from_utc_to_tenant()` in `app/helpers.php` — always use these for timezone conversion, never raw Carbon shifts.
- `TenantTimezoneDates` trait — apply to any model with date columns that need tenant-aware display.
- `spatie/laravel-query-builder` powers filterable/sortable index endpoints; custom sorts in `app/Http/Sorts/`.

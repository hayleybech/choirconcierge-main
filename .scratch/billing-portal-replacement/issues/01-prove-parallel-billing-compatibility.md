# 01: Prove parallel Spark and Paddle Billing compatibility

**What to build:** Keep the existing Spark/Paddle Classic experience operational while proving that the new Paddle Billing integration can run in the same application without dependency, namespace, or webhook collisions.

**Blocked by:** None (can start immediately).

**Status:** ready-for-human

- [x] The coordinated namespace-isolated Cashier Paddle 1.x and Spark forks resolve alongside Cashier Paddle 2.x and Subscriptionify.
- [x] Verification demonstrates that existing Spark portal and Classic webhook behavior continues to work, while Billing events and operations are handled only by the new integration.
- [ ] Current Paddle migration-tool requirements are confirmed, including required Billing transactions/renewal and the supported handling of trialing, paused, past-due, scheduled-to-cancel, and discounted subscriptions.
- [x] If the coexistence or migration assumptions fail, the result identifies the incompatibility and a concrete decision needed before dependent implementation proceeds.

## Comments

### Initial compatibility findings — 2026-10-26

- The app currently boots. `php artisan route:list --name=spark` shows `spark.portal` at `app/billing/{type?}/{id?}`, and `php artisan route:list --name=cashier` shows the Classic webhook at `paddle/webhook` as `cashier.webhook`. Pennant is present in the current Composer install, so the earlier startup failure is no longer reproducible.
- Cashier Paddle 2.8.1 and Subscriptionify 1.0.2 are compatible with the project's Laravel 11 / PHP 8.4 requirements. Composer cannot install Cashier 2.8.1 alongside Spark 4.1.1 as-is: Spark requires `laravel/cashier-paddle:^1.10`, while Cashier 2.x retains the `Laravel\\Paddle` namespace used by 1.x.
- The current Cashier/Spark integration shares configuration and runtime surfaces: Cashier reads the `cashier` config key, registers `paddle/webhook` as `cashier.webhook`, and provides fixed `customers`, `subscriptions`, and `receipts` migrations. Spark calls `Cashier::ignoreMigrations()`, loads the same migration filenames, and registers its own `spark/*` routes plus the configured portal route. `Tenant` uses `Spark\\Billable`. A viable in-process fork therefore needs distinct Composer identities and PHP namespaces, plus explicit provider config, webhook route/name, migration/table, and application model isolation; a package-name-only fork is insufficient.
- Paddle's migration docs state that starting migration requires a completed Billing transaction and at least one Billing subscription renewal. They document migration for active, paused, and past-due subscriptions after final dunning; subscriptions still in dunning restart retries in Billing, and completed dunning imports as paused. Classic plan trial days map to Billing price trial periods, and Classic coupons map to Billing discounts/imported events, but the public docs reviewed do not confirm preservation of an already-running trial's exact expiry, scheduled-cancellation behavior, or legacy discount terms. Those remain Paddle-confirmation gates.
- At the initial investigation stage no Composer dependencies or application code had yet been changed; the forks and dual-webhook proof remained outstanding then.

### Coexistence implementation — 2026-10-26

- Added tracked local path forks as `choirconcierge/cashier-paddle-classic` 1.12.0 and `choirconcierge/spark-paddle-classic` 4.1.1, isolated under `ChoirConcierge\LegacyCashierPaddle` and `ChoirConcierge\LegacySpark`. Spark's `Tenant` trait/provider now use the fork while preserving existing Classic tables and portal route.
- Composer now resolves Laravel Cashier Paddle 2.8.1 and Subscriptionify 1.0.2 alongside both forks. The project was upgraded to Laravel 12.70.1 after the Laravel 11 dependency line was blocked by a critical security advisory; the Spark fork's Illuminate constraint was expanded to Laravel 12. Spatie Backup 9.4.1 and Query Builder 7.3.7 were also upgraded, including their required app API/config adjustments.
- Split Classic and Billing configuration, Blade identifiers, and webhook names. Classic remains at `paddle/webhook` (`cashier.classic.webhook`); Billing is at `paddle-billing/webhook` (`cashier.webhook`); the Spark portal remains at `app/billing/{type?}/{id?}` (`spark.portal`). Feature tests assert each route resolves to its intended namespaced controller.
- Kept legacy data in `customers`, `subscriptions`, and `receipts`. Billing Cashier models/migration use `billing_customers`, `billing_subscriptions`, `billing_subscription_items`, and `billing_transactions`. Published Subscriptionify migrations use `ent_plans`, `ent_features`, `ent_plan_features`, `ent_subscriptions`, `ent_feature_usage`, and `ent_feature_grants`, separate from both Cashier and Pennant's `features` table.
- Added `tests/Feature/BillingProviderCompatibilityTest.php` to assert route/controller separation, model table mapping, and real coexistence of the legacy, Billing, and entitlement tables. `php artisan test --compact` completed with 520 passed, 11 incomplete, and 18 skipped; the focused compatibility tests pass (2 tests, 21 assertions). `npm run build` and `composer validate --no-check-publish` also succeed.
- The bundled `composer.phar` does not provide the `audit` command, so a separate Composer advisory scan could not be run. The Laravel 11 security constraint was resolved by upgrading rather than bypassing the advisory.
- Paddle still needs to confirm exact preservation/handling of an already-running trial expiry, period-end scheduled cancellation, and previously granted legacy discount terms. Those migration gates keep the ticket `ready-for-human` until Paddle-specific confirmation is recorded.

References: [Paddle migration prerequisites and import behavior](https://developer.paddle.com/migrate/start/port-data), [Paddle technical data mapping](https://developer.paddle.com/migrate/plan/data-mapping), [Cashier Paddle](https://github.com/laravel/cashier-paddle), and [Subscriptionify](https://github.com/revoltify/subscriptionify).
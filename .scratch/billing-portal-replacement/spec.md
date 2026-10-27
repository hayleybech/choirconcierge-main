# Billing portal replacement and staged migration

Status: ready-for-agent

## Problem Statement

Choir Concierge currently relies on Laravel Spark and Paddle Classic for tenant subscriptions. The product needs to move new subscriptions to Paddle Billing and provide an app-specific billing experience without interrupting existing subscriptions or charging a tenant through two billing systems at once. During the migration, application access checks must remain correct for tenants billed by either provider, and Spark must remain available until all subscriptions that still require billing have been migrated or intentionally ended.

## Solution

Introduce a Paddle Billing subscription experience for all new customers, using Laravel Cashier Paddle and Subscriptionify, while retaining the existing Spark/Paddle Classic path for legacy tenants during a controlled overlap. Keep one billing system of record per tenant and use Subscriptionify as the unified subscription-access interface across both paths.

Keep the app-side portal focused on product-specific plan information and plan changes. Use Paddle-hosted checkout and account-management flows for payment operations supported by Paddle. Preserve the existing commercial terms while adding permanent monthly options to all tiers on the new Billing path. Migrate eligible legacy subscriptions with Paddle’s migration tool, and switch a tenant’s billing ownership only after the import is confirmed and the Billing subscription is verified.

## User Stories

1. As a new tenant administrator, I want new subscriptions to be created through Paddle Billing, so that new business no longer depends on Spark.
2. As a tenant administrator, I want to choose Small, Medium, or Large, so that I can select a plan sized for my choir.
3. As a tenant administrator, I want to choose monthly or annual billing for each tier, so that I can balance payment flexibility and savings.
4. As a tenant administrator, I want annual billing to cost 20% less than twelve monthly payments, so that the longer commitment has a clear and consistent benefit.
5. As an eligible new customer, I want the annual-only `FIRSTYR` promotion to discount my first annual term by 10% in addition to annual savings, so that the advertised introductory offer is applied as agreed.
6. As a customer on a new monthly or annual plan, I want the same 30-day trial terms, so that trial access does not depend on my billing interval.
7. As a tenant administrator, I want to see my current plan, interval, features, and relevant product-specific billing context in Choir Concierge, so that I understand what my subscription provides.
8. As a Billing customer, I want to start checkout and manage supported payment details, invoices, and cancellation through Paddle-hosted flows, so that the app does not duplicate Paddle’s account-management experience.
9. As a Billing customer, I want to change my plan from Choir Concierge, so that I can upgrade or downgrade without contacting support.
10. As a Billing customer changing plans, I want changes to take effect immediately with prorated charges or credits, so that the billing adjustment reflects the time remaining in the current period.
11. As a legacy tenant administrator, I want to continue using Spark and my existing annual plan until migration, so that the replacement does not disrupt my current subscription.
12. As a legacy tenant administrator, I want monthly options to remain unavailable until my tenant is on Paddle Billing, so that the legacy catalog remains unchanged during coexistence.
13. As a tenant, I want exactly one billing system to own my subscription at a time, so that the same tenant is never charged by both systems.
14. As an application user, I want feature and subscription access checks to use Subscriptionify regardless of whether my tenant is still on Spark or has moved to Paddle Billing, so that access behaves consistently across the migration.
15. As an application user, I want my last confirmed subscription access to remain in effect during a short provider synchronization delay, so that a delayed event does not unexpectedly remove access.
16. As an administrator, I want delayed or missed provider events to be retried or reconciled into Subscriptionify, so that subscription access eventually reflects confirmed billing state.
17. As a Small or Medium tenant, I want the existing included-user limit, five-user buffer, and 30-day grace period to remain in effect, so that the replacement preserves the current overage policy.
18. As a Large tenant, I want to retain unlimited-user access, so that migration does not impose a new quota.
19. As a tenant currently covered by `has_gratis`, I want a no-cost Subscriptionify subscription, so that I retain access without a Paddle subscription or a separate bypass.
20. As a tenant administrator migrating during a trial, I want the replacement trial to end at the original Spark trial expiry, so that migration neither shortens nor restarts the promised trial.
21. As an administrator, I want a tenant’s billing ownership to switch only after Paddle confirms the import and the imported subscription is verified, so that a failed or incomplete import does not create incorrect billing ownership.
22. As an administrator, I want import failures and duplicate or delayed events to be safe to retry and reconcile, so that migration does not create duplicate subscriptions or charges.
23. As a customer who received the legacy 20% `FIRSTYR` offer, I want that offer honored through its promised term, so that migration does not change a promotion already granted.
24. As a customer with Paddle Billing invoices, I want to use Paddle’s hosted experience to view them, so that current invoice access is available without duplicating invoice management in Choir Concierge.
25. As an administrator, I want pre-migration Classic receipt records retained for administrative use but not shown in the new customer experience, so that historical records remain available internally without implying that Paddle Billing contains them.
26. As an administrator, I want resumable paused and past-due Spark subscriptions to be migrated or intentionally ended before Spark retirement, so that subscriptions needing future billing or collection are not abandoned.
27. As an administrator, I want active and trialing legacy subscriptions to be migrated or intentionally ended before Spark retirement, so that ongoing customer commitments are resolved.
28. As an administrator, I want canceled and expired subscriptions not to block Spark retirement, so that completed billing relationships do not delay the cutover.
29. As a tenant with a subscription scheduled to cancel at period end, I want access through the paid-through date without being needlessly migrated, so that the scheduled end is honored while cutover waits for the cancellation to expire.
30. As an administrator, I want Spark retirement gated on resolving every in-scope ongoing subscription, so that the legacy billing path is not removed prematurely.

## Implementation Decisions

- The existing Spark/Paddle Classic path and the replacement Paddle Billing path will operate in the same application during migration. The legacy path will use coordinated internal namespace-isolated forks of Cashier Paddle 1.x and Spark; the new path will use Laravel Cashier Paddle 2.x and Subscriptionify.
- Each tenant will have one billing owner at a time. New subscriptions belong to Paddle Billing; existing tenants remain Spark-owned until their import is confirmed and the imported subscription is verified.
- Subscriptionify is the common interface for application subscription-access checks. Legacy Spark subscription state is mirrored into it read-only while Spark remains the billing authority for those tenants. The last confirmed subscription access is retained during sync delays, with retry and reconciliation behavior for missed or delayed events.
- The new catalog will offer Small, Medium, and Large on both monthly and annual intervals. Monthly options are only available through Paddle Billing; Spark-owned tenants retain their existing annual-only experience until migration.
- Existing annual prices and tier limits remain unchanged. Monthly prices will be set so that annual billing is 20% less than twelve monthly payments; final currency amounts and provider price IDs are configuration inputs to confirm.
- `FIRSTYR` remains annual-only and changes to 10% off the first annual term. It stacks with the 20% annual savings, for 28% total first-year savings relative to twelve monthly payments. Any legacy customer already granted the prior 20% promotion keeps it through the promised term.
- Both new billing intervals use the existing 30-day trial. Spark no-card trials are not Paddle Classic subscriptions and are not imported by the Classic migration tool; if they are recreated in Billing, their original Spark expiry is preserved rather than restarting the full trial.
- Small and Medium retain included active-user quotas of 25 and 50 respectively, each with the existing five-user buffer and 30-day grace period. Large remains unlimited. Tenants covered by `has_gratis` receive a no-cost Subscriptionify subscription.
- The new portal will show app-specific plan and subscription-feature context. Paddle-hosted flows will handle checkout and supported payment-method, invoice, and cancellation operations. Choir Concierge will provide the Billing plan-change selection flow and request immediate changes with proration. Spark-owned tenants continue using Spark’s portal until migration.
- Classic receipts are not exposed to customers in the new experience; existing receipt records remain available to administrators. Paddle Billing-era invoices are accessed through Paddle.
- Migration will use Paddle’s Classic-to-Billing migration tool. Before migration begins, complete at least one Paddle Billing checkout/transaction and have at least one Paddle Billing subscription renew. Discounts can be migrated with the tool; during cutover, create any required Billing dashboard discount definitions before importing discounted subscriptions and verify previously granted terms are preserved for their promised remaining duration (ticket 05). Billing ownership changes only after a confirmed import and verification of the resulting subscription. Migration and event handling must be safe to retry and reconcile without creating duplicate customer subscriptions or charges.
- Active, paused, and past-due Spark subscriptions must be migrated or intentionally ended before Spark retirement. The only legacy trials in scope are Spark no-card trials, which are not Paddle Classic subscriptions; if recreated in Billing, their original Spark expiry must be preserved. Successful Billing imports silently cancel the corresponding Classic subscription without customer disruption; failed imports do not silently cancel it. Canceled and expired subscriptions do not block retirement. A subscription scheduled to cancel at period end cannot be migrated; it is excluded when no future billing is due, retains access through the paid-through date, and blocks final cutover completion until that period expires.
- Tests will use the feature-level request and provider-event seam confirmed during requirements clarification. They will assert externally observable customer and tenant outcomes while controlling only external Paddle and Subscriptionify interactions.

## Testing Decisions

- Good tests will verify externally observable behavior—responses, displayed billing options, ownership and subscription-access outcomes, and access decisions—rather than coupling assertions to provider SDK classes or internal implementation details.
- Pest feature tests will exercise tenant-facing billing requests and provider-event handling. Paddle and Subscriptionify interactions will be controlled at their integration boundaries so tests do not require live billing accounts or external network calls.
- Coverage will include new checkout eligibility and plan intervals; annual savings and coupon qualification; 30-day trial behavior; immediate prorated plan changes; legacy Spark routing; one-owner-at-a-time guarantees; confirmed-import cutover; preservation of Spark trial expiry; successful-import Classic cancellation and failed-import retention; delayed, duplicate, failed, and replayed events; subscription-access retention and reconciliation; quota buffers and grace periods; `has_gratis`; customer-visible invoice/receipt boundaries; and the Spark retirement gate for each relevant subscription status, including blocking final cutover until scheduled cancellations expire.
- Prefer the highest existing feature seam and avoid adding narrower seams unless required to control external provider behavior. Existing Pest feature tests already create tenant fixtures with subscriptions and assert tenant-facing settings and subscription-backed behavior; follow those conventions.

## Out of Scope

- Replacing Paddle with another payment processor or changing the product’s tier names, included-user limits, or existing annual prices.
- Rebuilding Paddle’s hosted payment-method, invoice, or cancellation interfaces inside Choir Concierge.
- Providing customer-facing access to pre-migration Classic receipts in the new portal.
- Making monthly plans available to tenants that remain Spark-owned.
- Retiring Spark before all subscriptions requiring ongoing billing have been migrated or intentionally ended.
- Broader product or pricing redesign beyond the monthly/annual parity and discount rules specified here.

## Further Notes

- The current application uses Spark 4.1.1 with Cashier Paddle 1.12.0 for Paddle Classic. Cashier 2.x is a different major integration, so the coordinated fork and namespace-isolation approach must be proven against Spark’s actual API usage and Composer resolution before implementation is treated as complete.
- Paddle has confirmed that migration requires at least one completed Billing checkout/transaction and one renewed Billing subscription, and that discounts can be migrated, though their definitions may need to be created in the Billing dashboard first. Paused and past-due subscriptions can be migrated, scheduled-to-cancel subscriptions cannot be migrated, and successful imports silently cancel Classic subscriptions unless the Billing import fails. Spark no-card trials are not Paddle Classic subscriptions. Discount setup and preservation verification are deferred to cutover under ticket 05; permanent monthly plans are intended to provide an earlier qualifying path than annual-only billing, not a temporary migration-only offer.
- Confirm the actual currency amounts and Paddle product/price identifiers for monthly plans, and verify coupon stacking before rollout. During cutover, ticket 05 will determine required dashboard discount definitions and verify preservation of previously granted legacy terms.
- Verify customer/billing-contact identity mapping during migration, including the existing relationship between a tenant’s billing contact and its user or membership records.
- The repository currently has no provider-independent billing abstraction; the implementation will need a clear tenant billing-owner and synchronization boundary without weakening the confirmed feature-level test seam.
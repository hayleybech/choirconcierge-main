# 05: Migrate Spark subscriptions without double billing

**What to build:** Move a legacy tenant’s subscription from Paddle Classic to Paddle Billing through Paddle’s migration flow, preserving its promised access and commercial terms while preventing duplicate billing and premature ownership changes.

**Blocked by:** 03: Start new tenant subscriptions on Paddle Billing.

**Status:** ready-for-agent

- [ ] Production imports do not begin until Paddle confirms the Billing integration has met its required completed-transaction and subscription-renewal prerequisites.
- [ ] Active, paused, and past-due subscriptions are imported when eligible or intentionally ended. Spark no-card trials are not Paddle Classic subscriptions, so the migration workflow handles them outside the Classic import and preserves their original Spark trial expiry if recreated in Billing; any already-granted legacy 20% `FIRSTYR` offer is honored through its promised term.
- [ ] A successful Billing import silently cancels the corresponding Classic subscription without customer disruption; a failed import does not silently cancel the Classic subscription.
- [ ] Subscriptions scheduled to cancel at period end are not imported; they retain access through the paid-through date, and final cutover completion waits until all pending cancellations expire.
- [ ] A tenant remains Spark-owned until Paddle confirms the import and the resulting Billing subscription is verified; failed imports do not cause an ownership switch.
- [ ] Import events and retries are idempotent and reconcilable, so delayed, duplicate, or replayed events cannot create duplicate subscriptions or charges.
- [ ] Feature tests cover successful and failed imports, trial/discount preservation, delayed and duplicate events, and ownership/entitlement outcomes.
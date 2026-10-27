# 02: Keep legacy and free-tenant entitlements unified

**What to build:** Ensure Spark-owned tenants and tenants covered by `has_gratis` retain their correct application access while Subscriptionify becomes the single interface used for entitlement checks.

**Blocked by:** 01: Prove parallel Spark and Paddle Billing compatibility.

**Status:** ready-for-agent

- [ ] Spark remains the billing authority for legacy tenants, while its confirmed subscription state is mirrored read-only into Subscriptionify.
- [ ] Application access checks use the last confirmed Subscriptionify entitlement during synchronization delays, and delayed or missed updates can be retried and reconciled.
- [ ] Small and Medium retain their 25- and 50-active-user quotas, five-user buffers, and 30-day grace periods; Large remains unlimited.
- [ ] Tenants covered by `has_gratis` receive a no-cost Subscriptionify entitlement and retain access without a Paddle subscription.
- [ ] Feature tests verify tenant access outcomes for valid, invalid, over-quota/grace-period, free, and temporarily stale entitlement states.
# 06: Gate Spark retirement on unresolved subscriptions

**What to build:** Allow Spark to be retired only after every legacy subscription that still requires ongoing billing has been migrated or intentionally ended, without cutting off customers who retain paid-through access.

**Blocked by:** 05: Migrate Spark subscriptions without double billing.

**Status:** ready-for-agent

- [ ] Retirement readiness identifies active, trialing, paused, and past-due Spark subscriptions that must be migrated or intentionally ended.
- [ ] Canceled and expired subscriptions do not block retirement; a subscription scheduled to cancel at period end is not migrated when no future billing is due, but blocks final cutover completion until its paid-through period expires.
- [ ] A tenant with a scheduled cancellation retains subscription access through the paid-through date and loses it when that period ends.
- [ ] Spark retirement and final cutover completion are blocked while any in-scope subscription remains unresolved or any scheduled cancellation is still within its paid-through period; they can proceed after those periods expire and all other in-scope subscriptions are resolved.
- [ ] Feature tests cover each relevant subscription status and verify both the readiness decision and paid-through access behavior.